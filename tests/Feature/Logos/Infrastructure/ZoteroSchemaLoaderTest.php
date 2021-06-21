<?php

declare(strict_types=1);

namespace Tests\Feature\Logos\Infrastructure;

use Tests\TestCase;
use Arete\Logos\Ports\Interfaces\ZoteroSchemaLoaderInterface;
use Arete\Logos\Models\Zotero\ZoteroSchema;

class ZoteroSchemaLoaderTest extends TestCase
{
    public function testZoteroSchemaLoaderIsBinded()
    {
        $loader = $this->app->make(ZoteroSchemaLoaderInterface::class);
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
