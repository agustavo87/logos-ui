<?php

declare(strict_types=1);

namespace Arete\Logos\Tests\Traits;

use Arete\Logos\Application\Ports\Interfaces\ComplexSourcesRepository;
use DateTime;

trait SourcesComplexFilterTest
{
    /**
     * @param ComplexSourcesRepository $sources
     *
     * @depends testComplexSourcesRepositoryIsBinded
     * @return
     */
    public function testComplexFilterTestRecivesComplexSourcesRepo(
        ComplexSourcesRepository $sources
    ): ComplexSourcesRepository {
        $this->assertInstanceOf(ComplexSourcesRepository::class, $sources);
        $this->seedSources($sources);
        return $sources;
    }

    /**
     * @param ComplexSourcesRepository $filter
     *
     * @depends testComplexFilterTestRecivesComplexSourcesRepo
     * @return ComplexSourcesRepository
     */
    public function testFilterByAttributes(ComplexSourcesRepository $sources): ComplexSourcesRepository
    {
        $params = [
            'attributes' => ['title' => 'gatos', 'abstractNote' => 'Dios']
        ];
        $godSource = $sources->complexFilter($params)[0];
        $this->assertEquals("Todos los gatos van al Cielo.", $godSource->title);
        return $sources;
    }

    /**
     * @param ComplexSourcesRepository $sources
     *
     * @depends testFilterByAttributes
     * @return ComplexSourcesRepository
     */
    public function testFilterByAuthor(ComplexSourcesRepository $sources): ComplexSourcesRepository
    {
        $result = $sources->complexFilter([
                'attributes' => [
                    'title' => 'gatos'
                ],
                'participations' => [
                    'author' => [
                        'attributes' => [
                            'name' => 'Magdalena Tamara'
                        ]
                    ]
                ],
            ]);

        $source = $result[0];

        $this->assertEquals("Todos los gatos van al Cielo.", $source->title);
        return $sources;
    }

    /**
     * @param ComplexSourcesRepository $sources
     *
     * @depends testFilterByAuthor
     * @return ComplexSourcesRepository
     */
    public function testFilterByRole(ComplexSourcesRepository $sources): ComplexSourcesRepository
    {
        $result = $sources->complexFilter([
                'attributes' => [
                    'title' => 'gatos'
                ],
                'participations' => [
                    'reviewedAuthor' => []
                ],
            ]);

        $source = $result[0];

        $this->assertEquals(
            "Roberto Miguel",
            $source->participations()->byRelevance('reviewedAuthor')[0]->name
        );
        return $sources;
    }

    /**
     * @param ComplexSourcesRepository $sources
     *
     * @depends testFilterByRole
     * @return ComplexSourcesRepository
     */
    public function testFilterByOwner(ComplexSourcesRepository $sources): ComplexSourcesRepository
    {
        $results = $sources->complexFilter([
            'ownerID' => '1',
            'participations' => [
                'author' => [
                    'attributes' => [
                        'name' => 'Magdalena Tamara'
                    ]
                ]
            ]
        ]);
        $this->assertGreaterThan(0, count($results));
        $source = $results[0];
        $this->assertEquals('Animal Metaphysics Handbook', $source->title);
        return $sources;
    }

    public static function seedSources(ComplexSourcesRepository $sources)
    {
        $sources->flush();

        $sources->createFromArray([
            'type' => 'journalArticle',
            'attributes' => [
                'title' => "Todos los gatos van al infierno.",
                'abstractNote' =>   "La historia del trato secreto de Lucifer con una especie diseñada " .
                                    "para dominar humanos.",
                'date' => new DateTime('01-01-1988'),
                'volume' => 4,
                'issue' => 3
            ]
        ], 3);

        $sources->createFromArray([
            'type' => 'journalArticle',
            'attributes' => [
                'title' => "Todos los gatos van al Cielo.",
                'abstractNote' =>   "La historia del trato secreto de Dios con una especie diseñada " .
                                    "para llevar a los humanos a ejercitar su paciencia.",
                'publicationTitle' => 'Journal of Trans-Species Metaphysics.',
                'date' => new DateTime('01-01-1988'),
                'volume' => 4,
                'issue' => 3
            ],
            'participations' => [
                [
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
        ], 3);

        $sources->createFromArray(
            [
                'type' => 'book',
                'attributes' => [
                    'title' => "Animal Metaphysics Handbook",
                    'publisher' => 'Gomez e Hijos Inc.',
                    'place' => 'Argentina'
                ],
                'participations' => [
                    [
                        'role' => 'author',
                        'relevance' => 2,
                        'creator' => [
                            'type' => 'person',
                            'attributes' => [
                                'name' => "Magdalena Tamara",
                                'lastName' => "Guiñazú"
                            ]
                        ]
                    ]
                ]
            ],
            1
        );
    }
}
