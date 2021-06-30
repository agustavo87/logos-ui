<?php

declare(strict_types=1);

namespace Arete\Logos\Tests;

use PHPUnit\Framework\TestCase;
use Arete\Logos\Application\LogosContainer;
use Arete\Logos\Application\Ports\Interfaces\ZoteroSchemaLoaderInterface;
use Arete\Logos\Domain\Zotero\ZoteroSchema;

class ZoteroSchemaLoaderTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        LogosContainer::load();
    }

    public function testZoteroSchemaLoaderIsBinded()
    {
        $loader = LogosContainer::get(ZoteroSchemaLoaderInterface::class);
        $this->assertInstanceOf(
            ZoteroSchemaLoaderInterface::class,
            $loader
        );

        return $loader;
    }
    /**
     * @depends testZoteroSchemaLoaderIsBinded
     *
     * @param mixed $loader
     *
     * @return [type]
     */
    public function testZoteroSchemaLoaderLoadsZoteroSchema($loader)
    {
        $schema = $loader->load();
        $this->assertInstanceOf(ZoteroSchema::class, $schema);
        return $schema;
    }

    /**
     * @depends testZoteroSchemaLoaderLoadsZoteroSchema
     *
     * @param mixed $schema
     *
     * @return [type]
     */
    public function testZoteroSchemaHaveSomeData($schema)
    {
        $itemTypes = $schema->itemTypes;
        $expectedTypes = ['annotation', 'blogPost', 'book', 'bookSection', 'journalArticle'];
        $dif = array_diff($expectedTypes, $itemTypes); // ItemType's are stringable
        $this->assertEquals(0, count($dif));
    }
}
