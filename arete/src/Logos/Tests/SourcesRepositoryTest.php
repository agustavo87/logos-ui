<?php

declare(strict_types=1);

namespace Arete\Logos\Tests;

use Arete\Logos\Application\LogosContainer;
use Arete\Logos\Application\Ports\Interfaces\SourcesRepository;
use Arete\Logos\Application\TestSourcesProvider;
use Arete\Logos\Domain\Abstracts\SourceType;
use Arete\Logos\Domain\Contracts\Participation;
use Arete\Logos\Domain\ParticipationSet;
use Arete\Logos\Domain\Source;
use PHPUnit\Framework\TestCase;
use Faker\Generator;

class SourcesRepositoryTest extends TestCase
{
    public static SourcesRepository $sources;
    protected Generator $faker;

    public function __construct(...$args)
    {
        parent::__construct(...$args);
        $this->faker = \Faker\Factory::create('es_AR');
    }

    public static function setUpBeforeClass(): void
    {
        // boot container
        // Loads CreatorTypeRepository by default and other basic/testing adapters.
        LogosContainer::pushProvider(TestSourcesProvider::class);
        LogosContainer::load();
        self::$sources = LogosContainer::get(SourcesRepository::class);
    }

    public function testCreatorTypeRepositoryTestIsBinded()
    {
        $this->assertInstanceOf(SourcesRepository::class, self::$sources);
    }

        /**
    * Test de creation of a new source
    *
    * @return Source
    */
    public function testCreatesSourceWithoutCreator(): Source
    {
        $sources = self::$sources;
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
        $sources = self::$sources;
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
     * Test if get a source correctly
     *
     * @param Source $source
     *
     * @depends testGetSameAndDiferentInstancesOfSameSource
     * @return Source
     */
    public function testGetSource(Source $storedSource): Source
    {
        $sources = self::$sources;
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
        $sources = self::$sources;
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
        $sources = self::$sources;
        $originalAbstract = $storedSource->abstractNote;
        // generete an proabably unique key to look for
        $randomWords = $this->faker->word() . ' ' . str_shuffle($this->faker->word());
        $storedSource->abstractNote .= ' ' . $randomWords;
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
        $sources = self::$sources;
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

        $sources = self::$sources;
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
        $sources = self::$sources;

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
}
