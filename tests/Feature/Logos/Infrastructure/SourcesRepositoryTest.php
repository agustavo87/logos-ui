<?php

declare(strict_types=1);

namespace Tests\Feature\Logos\Infrastructure;

use App\Models\User;
use Tests\TestCase;
use Arete\Logos\Application\LogosContainer;
use Arete\Logos\Application\Ports\Interfaces\SourcesRepository;
use Arete\Logos\Application\Ports\Interfaces\ComplexSourcesRepository;
use Arete\Logos\Application\Ports\Interfaces\LogosEnviroment;
use Arete\Logos\Domain\Source;
use Arete\Logos\Domain\Abstracts\SourceType;
use Arete\Logos\Domain\ParticipationSet;
use Arete\Logos\Domain\Contracts\Participation;
use Arete\Logos\Tests\Traits\SourcesComplexFilterTest;
use Faker\Generator;

class SourcesRepositoryTest extends TestCase
{
    use SourcesComplexFilterTest;

    protected Generator $faker;

    public function __construct(...$args)
    {
        parent::__construct(...$args);
        $this->faker = \Faker\Factory::create('es_AR');
    }

    public function testSourcesRepositoryIsBinded()
    {
        $sources = $this->app->make(SourcesRepository::class);
        $this->assertInstanceOf(SourcesRepository::class, $sources);
        return $sources;
    }

    /**
    * Test de creation of a new source
    *
    * @return Source
    */
    public function testCreatesSourceWithoutCreator(): Source
    {
        /** @var SourcesRepository */
        $sources = $this->app->make(SourcesRepository::class);
        $name = $this->faker->name();
        $sourceData = [
            'type' => 'journalArticle',
            'attributes' => [
                'title' => "El despertar de {$name} a la atención plena.",
                'abstractNote' => "La historia de como {$name} despertó a la atención plena",
                'date' => now(),
                'accessDate' => now(),
                'volume' => $this->faker->numberBetween(1, 50),
                'issue' => $this->faker->numberBetween(1, 4)
            ]
        ];
        $source = $sources->createFromArray($sourceData);
        $this->checkSourceDataStructure($source, $sourceData['attributes']);
        return $source;
    }



    /**
     * @param Source $source
     *
     * @depends testCreatesSourceWithoutCreator
     * @return Source
     */
    public function testGetSameAndDiferentInstancesOfSameSource(Source $previousSource): Source
    {
        /** @var SourcesRepository */
        $sources = $this->app->make(SourcesRepository::class);

        // when retrieve a already retrieved sourced, it returns the reference to the same object.
        $firstFetch = $sources->get($previousSource->id());
        $secondFetch = $sources->get($previousSource->id());
        $this->assertSame($firstFetch, $secondFetch);

        // getNew(), gets a new instance of the same source.
        $newFetch = $sources->getNew($previousSource->id());
        $this->assertNotSame($firstFetch, $newFetch);

        return $previousSource;
    }

    /**
     * @param Source $previousSource
     *
     * @depends testGetSameAndDiferentInstancesOfSameSource
     * @return Source
     */
    public function testGetDifferentKeySuggestionIfExist(Source $previousSource): Source
    {
        /** @var SourcesRepository */
        $sources = $this->app->make(SourcesRepository::class);

        /** @var \Arete\Logos\Application\Ports\Interfaces\LogosEnviroment */
        $env =  $this->env = $this->app->make(LogosEnviroment::class);

        /** @var \App\Models\User */
        $userA = User::factory()->create();
        /** @var \App\Models\User */
        $userB = User::factory()->create();

        $alienKeyA = 'fakeAlienKey123A';
        $alienKeyB = 'fakeAlienKey123B';

        $sources->createFromArray([
            'key' => $alienKeyA,
            'type'  => 'journalArticle',
            'attributes' => [
                'title' => "El despertar de Matsurana a la atención plena."
            ]
        ], $userA->id);
        $sources->createFromArray([
            'key' => $alienKeyB,
            'type'  => 'journalArticle',
            'attributes' => [
                'title' => "El despertar de Matsurana a la atención plena."
            ]
        ], $userA->id);

        $this->actingAs($userB);
        $this->assertEquals($userB->id, $env->getOwner());

        $newKey = $sources->getKey($alienKeyA);                 // now the key NOT exist so
        $this->assertEquals($alienKeyA, $newKey);      // should return the same key

        $ownSourceA = $sources->createFromArray([
            'key' => $alienKeyA, // The same key can be repeated in different users
            'type'  => 'journalArticle',
            'attributes' => [
                'title' => "El despertar de Matsurana a la atención plena."
            ]
        ]);
        $this->assertEquals($ownSourceA->key(), $alienKeyA);

        $newKey = $sources->getKey($alienKeyA);                 // now the key exist so
        $this->assertNotEquals($alienKeyA, $newKey);    // a diferent key should be suggested.

        $otherAlienKey = $sources->getKey([
            'ownerID' => $userA->id,                    // the key in this user exist
            'key'     => $alienKeyB                     // so a different key should be sugested.
        ]);

        $this->assertNotEquals($alienKeyB, $otherAlienKey);

        self::$testOwnerID['A'] = $userA->id;
        self::$testOwnerID['B'] = $userB->id;
        return $previousSource;
    }

    /**
     * Test if get a source correctly
     *
     * @param Source $source
     *
     * @depends testGetSameAndDiferentInstancesOfSameSource
     * @return Source
     */
    public function testGetSource(Source $storedSource): Source
    {
        /** @var SourcesRepository */
        $sources = $this->app->make(SourcesRepository::class);
        $fetchedSource = $sources->getNew($storedSource->id());
        $this->checkSourceDataStructure($fetchedSource, $storedSource->toArray()['attributes']);
        return $storedSource;
    }

    /**
     * @param Source $storedSource
     *
     * @depends testGetSource
     * @return Source
     */
    public function testSaveSource(Source $storedSource): Source
    {
        /** @var SourcesRepository */
        $sources = $this->app->make(SourcesRepository::class);
        $storedSource->abstractNote = "Cuenta la historia de como tu abuela le gusta le gusta andar en patineta";
        $storedSource->volume = 32;
        $sources->save($storedSource);

        // fetch new so test if changed in persistence
        $fetchedSource = $sources->getNew($storedSource->id());
        $this->assertEquals(
            "Cuenta la historia de como tu abuela le gusta le gusta andar en patineta",
            $fetchedSource->abstractNote
        );
        $this->assertEquals(32, $fetchedSource->volume);

        return $fetchedSource;
    }

    /**
     * @depends testSaveSource
     * @return Source
     */
    public function testGetLikeSource(Source $storedSource): Source
    {
        /** @var SourcesRepository */
        $sources = $this->app->make(SourcesRepository::class);
        $originalAbstract = $storedSource->abstractNote;
        // generete an proabably unique key to look for
        $randomWords = $this->faker->word() . ' ' . str_shuffle(implode('', $this->faker->words(6, false)));
        $storedSource->abstractNote .= ' << ' . $randomWords . ' >>.';
        $sources->save($storedSource);

        // search by the key
        $source = $sources->getLike('abstractNote', $randomWords)[0];
        // has the generated key
        $this->assertStringContainsString(
            $randomWords,
            $source->abstractNote
        );
        // if is as the previous source
        $this->assertStringContainsString(
            $originalAbstract,
            $source->abstractNote
        );
        $this->assertEquals(32, $source->volume);
        return $source;
    }

    /**
     * @param Source $source
     *
     * @depends testGetLikeSource
     * @return void
     */
    public function testRemovesSourceWithoutCreators(Source $source)
    {
        /** @var SourcesRepository */
        $sources = $this->app->make(SourcesRepository::class);
        $sources->remove($source->id());
        $this->assertFalse($sources->keyExist($source->key()));
    }

    public function checkSourceDataStructure(Source $source, array $expectedAttributes = []): void
    {
        $this->assertInstanceOf(Source::class, $source);
        $this->assertIsInt($source->id());
        $type = $source->type();
        $this->assertInstanceOf(SourceType::class, $type);
        $this->assertInstanceOf(ParticipationSet::class, $source->participations());
        $arraySource = $source->toArray();
        $this->assertIsArray($arraySource);
        // Log::info($arraySource);
        foreach ($expectedAttributes as $code => $value) {
            $attribute = $source->$code;
            $this->assertEquals($value, $attribute);
        }
    }

    /**
    * Test de creation of a new source
    *
    * @return Source
    */
    public function testCreatesSourceWithCreator(): Source
    {
        /** @var SourcesRepository */
        $sources = $this->app->make(SourcesRepository::class);
        $name = $this->faker->name();
        $sourceData = [
            'type' => 'book',
            'attributes' => [
                'title' => "Las mil y unas novias de {$name}",
                'abstractNote' =>   "Cuenta la historia de como {$name} paso de no tener a nadie a"
                                    . " estar rodeado de mujeres",
                'date' => now(),
                'edition' => '2nd',
                'place' => 'Buenos Aires',
                'publisher' => 'Agora',
                'volume' => $this->faker->numberBetween(1, 50),
            ],
            'participations' => [
                [
                    'role' => 'author',
                    'relevance' => 1,
                    'creator' => [
                        'type' => 'person',
                        'attributes' => [
                            'name' => "Pedro Eustaquio",
                            'lastName' => "Zamudio"
                        ]
                    ]
                ], [
                    'role' => 'author',
                    'relevance' => 2,
                    'creator' => [
                        'type' => 'person',
                        'attributes' => [
                            'name' => "Marisol Lucrecia",
                            'lastName' => "Bermudez"
                        ]
                    ]
                ]
            ]
        ];
        $source = $sources->createFromArray($sourceData);
        $this->checkSourceDataStructure($source, $sourceData['attributes']);

        $participations = $source->participations();
        // is the only role, if itsn't there's no guarantee of the roles order.
        $this->assertEquals('author', $participations->roles()[0]);
        $this->assertSame($participations->source(), $source);
        $authors = $participations->byRelevance('author');
        $firstAuthor = $authors[0];
        $this->assertInstanceOf(Participation::class, $firstAuthor);
        $this->assertEquals('person', $firstAuthor->creatorType()->code());
        $this->assertEquals('Pedro Eustaquio', $firstAuthor->name);
        $this->assertEquals('Zamudio', $firstAuthor->lastName);

        return $source;
    }

    /**
     * @param SourcesRepository $sources
     *
     * @depends testSourcesRepositoryIsBinded
     * @return SourcesRepository
     */
    public function testCreateAndGetSourceByKeyName(SourcesRepository $sources): SourcesRepository
    {
        $name = $this->faker->name();
        $sourceData = [
            'key' => 'procopio2021',
            'type' => 'book',
            'attributes' => [
                'title' => "Las flipantes aventuras de {$name} en la casa del oso",
                'abstractNote' =>   "Trata la historia de como {$name} se las vio en medio de una cuarentena de osos.",
                'date' => now(),
                'edition' => '2nd',
                'place' => 'Buenos Aires',
                'publisher' => 'Agora',
            ],
            'participations' => [
                [
                    'role' => 'author',
                    'relevance' => 1,
                    'creator' => [
                        'type' => 'person',
                        'attributes' => [
                            'name' => "Mario Cannario",
                            'lastName' => "Procopio"
                        ]
                    ]
                ]
            ]
        ];
        $source = $sources->createFromArray($sourceData);
        $this->checkSourceDataStructure($source, $sourceData['attributes']);

        $fetchedSource = $sources->getByKey($source->key());
        $this->assertEquals($source->id(), $fetchedSource->id());
        return $sources;
    }

    /**
     * @param Source $creator
     *
     * @depends testCreatesSourceWithCreator
     * @return Source
     */
    public function testCreateSourceWithCreatedCreator(Source $previousSource): Source
    {
        $previousAuthor = $previousSource
                    ->participations()
                    ->byRelevance('author')[0];

        /** @var SourcesRepository */
        $sources = $this->app->make(SourcesRepository::class);
        $newSource = $sources->createFromArray([
            'type' => 'journalArticle',
            'attributes' => [
                'title' => 'Examen de los pormenores de las dificultades gástricas de los lisiados en guerra.',
                'abstractNote' =>   'Se examina los pormenores de las dificultades gastroinstetianles de ' .
                                    'los soldados caidos en guerra pero que no llegaron a morir. Solo se torpezaron.',
                'date' => now(),
                'publicationTitle' => 'Studies in the gastrointestinal diseases',
                'volume' => 5,
                'issue' => 2,
                'pages' => '223-244'
            ],
            'participations' => [
                [
                    'role' => 'author',
                    'relevance' => 1,
                    'creator' => [
                        'creatorID' => $previousAuthor->creatorId()
                    ]
                ], [
                    'role' => 'author',
                    'relevance' => 2,
                    'creator' => [
                        'type' => 'person',
                        'attributes' => [
                            'name' => "Magdalena Tamara",
                            'lastName' => "Guiñazú"
                        ]
                    ]
                ], [
                    'role' => 'reviewedAuthor',
                    'relevance' => 4,
                    'creator' => [
                        'type' => 'person',
                        'attributes' => [
                            'name' => "Roberto Miguel",
                            'lastName' => "García"
                        ]
                    ]
                ]
            ]
        ]);

        // first reviewed author
        $reviewedAuthors = $newSource->participations()->reviewedAuthor;
        $firstReviewedAuthorID = array_key_first($reviewedAuthors);
        $this->assertEquals(
            'Roberto Miguel',
            $reviewedAuthors[$firstReviewedAuthorID]->name
        );
        // most relevant author
        $this->assertEquals(
            'Pedro Eustaquio',
            $newSource->participations()->byRelevance('author')[0]->name
        );

        return $newSource;
    }

    /**
     * @param Source $previousSource
     *
     * @depends testCreateSourceWithCreatedCreator
     * @return Source
     */
    public function testRemoveParticipation(Source $previousSource): Source
    {
        $firstAuthor = $previousSource->participations()->byRelevance('author')[0];
        $this->assertEquals(
            'Pedro Eustaquio',
            $firstAuthor->name
        );
        $previousSource->participations()->remove('author', $firstAuthor->creatorId());
        // now the most relevant author is other
        $newFirstAuthor = $previousSource->participations()->byRelevance('author')[0];
        $this->assertEquals(
            "Magdalena Tamara",
            $newFirstAuthor->name
        );

        return $previousSource;
    }

    /**
     * @param Source $previousSource
     *
     * @depends testRemoveParticipation
     * @return Source
     */
    public function testSaveSourceParticipation(Source $previousSource): Source
    {
        /** @var SourcesRepository */
        $sources = $this->app->make(SourcesRepository::class);

        // modify first author
        $firstAuthor = $previousSource
            ->participations()
            ->byRelevance('author')[0];
        $firstAuthor->setRelevance(5)
                    ->name = 'Eustequia Murcia';

        // ad a new most relevant (first) author
        $previousSource
            ->participations()
            ->pushNew(
                [
                    'type'  => 'person',
                    'attributes' => [
                        'name' => 'Roberto Pedro',
                        'lastName' => "Gonzalez"
                    ]
                ],
                'author',
                1
            );
        $sources->save($previousSource);

        $fetchedSource = $sources->getNew($previousSource->id());

        // the first (most relevant) author has changed
        $this->assertEquals(
            'Roberto Pedro',
            $fetchedSource->participations()->byRelevance('author')[0]->name
        );

        // the creator attributes were modified (and persisted).
        $this->assertEquals(
            'Eustequia Murcia',
            $fetchedSource->participations()->author[$firstAuthor->creatorId()]->name
        );
        return $fetchedSource;
    }

    /**
     * @param Source $previousSource
     *
     * @depends testSaveSourceParticipation
     * @return Source
     */
    public function testChangeRoleOfParticipation(Source $previousSource): Source
    {
        /** @var SourcesRepository */
        $sources = $this->app->make(SourcesRepository::class);

        $firstAuthor = $previousSource
            ->participations()
            ->byRelevance('author')[0];

        $firstAuthor->setRole($previousSource->type()->roles()->contributor);

        $sources->save($previousSource);

        $newSource = $sources->getNew($previousSource->id());

        $this->assertEquals(
            $firstAuthor->creator()->toArray(),
            $newSource->participations()->contributor[$firstAuthor->creatorId()]->creator()->toArray()
        );
        $this->assertNotEquals(
            $firstAuthor->creatorId(),
            $newSource->participations()->byRelevance('author')[0]->creatorId()
        );

        return $newSource;
    }

        /**
     * @param Source $previousSource
     *
     * @depends testChangeRoleOfParticipation
     * @return Source
     */
    public function testChangeRoleOfParticipationAndEmptyPreviousRole(Source $previousSource): Source
    {
        /** @var SourcesRepository */
        $sources = $this->app->make(SourcesRepository::class);

        $reviewedAuthor = $previousSource
            ->participations()
            ->byRelevance('reviewedAuthor')[0];

        $reviewedAuthor->setRole($previousSource->type()->roles()->contributor);

        $sources->save($previousSource);

        $newSource = $sources->getNew($previousSource->id());

        $this->assertEquals(
            $reviewedAuthor->creator()->toArray(),
            $newSource->participations()->contributor[$reviewedAuthor->creatorId()]->creator()->toArray()
        );
        $this->assertFalse($newSource->participations()->has('reviewedAuthor'));

        return $newSource;
    }

    /**
     * @param Source $source
     *
     * @depends testChangeRoleOfParticipation
     * @return void
     */
    public function testRemoveSourceWithParticipation(Source $source)
    {
        /** @var SourcesRepository */
        $sources = $this->app->make(SourcesRepository::class);
        $sources->remove($source->id());
        $this->assertFalse($sources->keyExist($source->key()));
    }

    public function testComplexSourcesRepositoryIsBinded(): ComplexSourcesRepository
    {
        $complexSource = LogosContainer::get(ComplexSourcesRepository::class);
        $this->assertInstanceOf(ComplexSourcesRepository::class, $complexSource);
        return $complexSource;
    }
}
