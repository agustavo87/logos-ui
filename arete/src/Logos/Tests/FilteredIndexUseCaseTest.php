<?php

declare(strict_types=1);

namespace Arete\Logos\Tests;

use PHPUnit\Framework\TestCase;
use Arete\Logos\Application\LogosContainer;
use Arete\Logos\Application\Ports\Interfaces\FilteredIndexUseCase;
use Arete\Logos\Application\Ports\Interfaces\SourcesRepository;
use Arete\Logos\Application\TestSourcesProvider;
use DateTime;

class FilteredIndexUseCaseTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        // boot container
        LogosContainer::pushProvider(TestSourcesProvider::class);
        LogosContainer::load();
        self::seedSources();
    }

    public static function seedSources()
    {
        /** @var SourcesRepository */
        $sources = LogosContainer::get(SourcesRepository::class);
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

    public function testSourcesPersited()
    {
        /** @var SourcesRepository */
        $sources = LogosContainer::get(SourcesRepository::class);
        $source = $sources->getLike('title', 'gatos')[0];
        $this->assertEquals("Todos los gatos van al infierno.", $source->title);
    }

    public function testTestBindingsWorking(): FilteredIndexUseCase
    {
        $filteredIndexUC = LogosContainer::get(FilteredIndexUseCase::class);
        $this->assertInstanceOf(FilteredIndexUseCase::class, $filteredIndexUC);
        return $filteredIndexUC;
    }

    /**
     * @param FilteredIndexUseCase $filter
     *
     * @depends testTestBindingsWorking
     * @return FilteredIndexUseCase
     */
    public function testFilterByAttributes(FilteredIndexUseCase $filter): FilteredIndexUseCase
    {
        $params = [
            'attributes' => ['title' => 'gatos', 'abstractNote' => 'Dios']
        ];
        $godSource = $filter->filter($params)[0];
        $this->assertEquals("Todos los gatos van al Cielo.", $godSource->title);
        return $filter;
    }

    /**
     * @param FilteredIndexUseCase $filter
     *
     * @depends testFilterByAttributes
     * @return FilteredIndexUseCase
     */
    public function testFilterByAuthor(FilteredIndexUseCase $filter): FilteredIndexUseCase
    {
        $result = $filter->filter([
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
        return $filter;
    }

    /**
     * @param FilteredIndexUseCase $filter
     *
     * @depends testFilterByAuthor
     * @return FilteredIndexUseCase
     */
    public function testFilterByRole(FilteredIndexUseCase $filter): FilteredIndexUseCase
    {
        $result = $filter->filter([
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
        return $filter;
    }

    /**
     * @param FilteredIndexUseCase $filter
     *
     * @depends testFilterByRole
     * @return FilteredIndexUseCase
     */
    public function testFilterByOwner(FilteredIndexUseCase $filter): FilteredIndexUseCase
    {
        $results = $filter->filter([
            'ownerID' => '1'
        ]);
        $this->assertGreaterThan(0, count($results));
        $source = $results[0];
        $this->assertEquals('Animal Metaphysics Handbook', $source->title);
        return $filter;
    }

    /**
     * @param FilteredIndexUseCase $filter
     *
     * @depends testFilterByOwner
     * @return FilteredIndexUseCase
     */
    public function testThrowsOnInexistentSourceType(FilteredIndexUseCase $filter): FilteredIndexUseCase
    {
        $this->expectExceptionCode(22);
        $filter->filter(['type' => 'booking']);
        return $filter;
    }

    /**
     * @param FilteredIndexUseCase $filter
     *
     * @depends testFilterByOwner
     * @return FilteredIndexUseCase
     */
    public function testThrowsOnInexistentAttributeType(FilteredIndexUseCase $filter): FilteredIndexUseCase
    {
        $this->expectExceptionCode(23);
        $filter->filter([
            'attributes' => [
                'nombrensioni' => 'noexisto'
            ]
        ]);
        return $filter;
    }
}
