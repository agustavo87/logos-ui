<?php

declare(strict_types=1);

namespace Tests\Feature\Logos;

use Tests\FixturableTestCase;

class ZoteroSchemaLoaderTest extends FixturableTestCase
{
    public function testZoteroSchemaLoaderIsBinded()
    {
        $loader = $this->app->make(\Arete\Logos\Services\Zotero\SchemaLoaderInterface::class);
        $this->assertInstanceOf(
            \Arete\Logos\Services\Zotero\SchemaLoaderInterface::class,
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
    public function testZoteroSchemaLoaderLoadsSchema($loader)
    {
        $schema = $loader->load();
        $this->assertInstanceOf(\Arete\Logos\Models\Zotero\Schema::class, $schema);
        return $schema;
    }

    /**
     * @depends testZoteroSchemaLoaderLoadsSchema
     *
     * @param mixed $schema
     *
     * @return [type]
     */
    public function testSchemaHaveSomeData($schema)
    {
        // print_r($schema);
        $itemTypes = $schema->itemTypes;
        $expectedTypes = ['annotation', 'blogPost', 'book', 'bookSection', 'journalArticle'];
        // print_r($itemTypes);
        foreach ($expectedTypes as $expType) {
            $matches = array_filter($itemTypes, function ($type) use ($expType) {
                return $type->itemType == $expType;
            });
            $this->assertGreaterThan(0, count($matches));
        }
    }
}
