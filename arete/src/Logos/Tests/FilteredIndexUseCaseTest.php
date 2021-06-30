<?php

declare(strict_types=1);

namespace Arete\Logos\Tests;

use Arete\Logos\Application\LogosContainer;
use PHPUnit\Framework\TestCase;
use Arete\Logos\Application\Ports\Logos;
use Arete\Logos\Application\TestSourcesProvider;

class FilteredIndexUseCaseTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        // boot container
        LogosContainer::pushProvider(TestSourcesProvider::class);
        LogosContainer::load();
    }


    public function testTestBindingsWorking()
    {
        $result = Logos::filteredIndex([
            'title' => 'a'
        ]);
        $this->assertEquals('Hola', $result);
    }
}
