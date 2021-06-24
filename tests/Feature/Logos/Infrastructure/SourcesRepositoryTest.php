<?php

declare(strict_types=1);

namespace Tests\Feature\Logos\Infrastructure;

use Arete\Logos\Domain\ParticipationSet;
use Arete\Logos\Domain\Abstracts\SourceType;
use Arete\Logos\Domain\Source;
use Tests\TestCase;
use Arete\Logos\Application\Ports\Interfaces\SourcesRepository;
use Faker\Generator;

class SourcesRepositoryTest extends TestCase
{
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
        // faltaría el usuario al que estaría asociada la fuente
        ];
        $source = $sources->createFromArray($sourceData);
        $this->checkSourceDataStructure($source, $sourceData['attributes']);
        return $source;
    }

    /**
     * Test if get a source correctly
     *
     * @param Source $source
     * @depends testCreatesSourceWithoutCreator
     * @return Source
     */
    public function testGetSource(Source $storedSource): Source
    {
        /** @var SourcesRepository */
        $sources = $this->app->make(SourcesRepository::class);
        $fetchedSource = $sources->get($storedSource->id());
        $this->checkSourceDataStructure($fetchedSource, $storedSource->toArray());
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

        // fetch
        $fetchedSource = $sources->get($storedSource->id());
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
        $source = $sources->getLike(1, 'abstractNote', 'abuela')[0];
        $this->assertEquals(
            "Cuenta la historia de como tu abuela le gusta le gusta andar en patineta",
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
        /** @var SourcesRepository */
        $sources = $this->app->make(SourcesRepository::class);
        $name = $this->faker->name();
        $sourceData = [
            'type' => 'book',
            'attributes' => [
                'title' => "Las mil y unas novias de {$name}.",
                'abstractNote' =>   "Cuenta la historia de como {$name} paso de no tener a nadie a"
                                    . " estar rodeado de mujeres.",
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
                    ->byRelevance('author')[0]
                    ->creator();

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
                        'creatorID' => $previousAuthor->id()
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

        $reviewedAuthors = $newSource->participations()->reviewedAuthor;
        $firstReviewedAuthorID = array_key_first($reviewedAuthors);
        $this->assertEquals('Roberto Miguel', $reviewedAuthors[$firstReviewedAuthorID]->name);
        $this->assertEquals('Pedro Eustaquio', $newSource->participations()->byRelevance('author')[0]->name);

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
        $firstAuthor = $previousSource->participations()->byRelevance('author')[0]->creator();
        $previousSource->participations()->remove('author', $firstAuthor->id());
        $this->assertEquals(
            "Magdalena Tamara",
            $previousSource->participations()->byRelevance('author')[0]->name
        );

        return $previousSource;
    }
}