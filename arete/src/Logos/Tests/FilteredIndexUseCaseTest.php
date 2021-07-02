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
        ]);
        $sources->createFromArray([
            'type' => 'journalArticle',
            'attributes' => [
                'title' => "Todos los gatos van al Cielo.",
                'abstractNote' =>   "La historia del trato secreto de Dios con una especie diseñada " .
                                    "para llevar a los humanos a ejercitar su paciencia.",
                'date' => new DateTime('01-01-1988'),
                'volume' => 4,
                'issue' => 3
            ]
        ]);
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
    public function testFilteredIndexFilterAttributes(FilteredIndexUseCase $filter): FilteredIndexUseCase
    {
        $params = [
            'attributes' => ['title' => 'gatos', 'abstractNote' => 'Dios']
        ];
        $godSource = $filter->filter($params)[0];
        $this->assertEquals("Todos los gatos van al Cielo.", $godSource->title);
        return $filter;
    }
}
