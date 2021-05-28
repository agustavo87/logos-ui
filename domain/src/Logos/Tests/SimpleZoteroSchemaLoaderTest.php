<?php

declare(strict_types=1);

namespace Arete\Logos\Tests;

use PHPUnit\Framework\TestCase;
use Tests\LogsInformation;
use Arete\Logos\Tests\Traits\ChecksZoteroSchemaDataStructure;
use Arete\Logos\Models\Zotero\{Schema, CSLMap};

class SimpleZoteroSchemaLoaderTest extends TestCase
{
    use LogsInformation;
    use ChecksZoteroSchemaDataStructure;

    public function testSchemaHaveExpectedDataStructure()
    {
        $schemaLoader = new \Arete\Logos\Services\Zotero\SimpleSchemaLoader();
        $this->isInstanceOf(\Arete\Logos\Services\Zotero\SchemaLoaderInterface::class, $schemaLoader);

        $schema = $schemaLoader->load();
        $this->checkSchemaDataStructure($schema);

        return $schema;
    }

    /**
     * @depends testSchemaHaveExpectedDataStructure
     *
     * @param Schema $schema
     *
     * @return Schema
     */
    public function testSchemaTypeHaveExpectedDataStrcture(Schema $schema): Schema
    {
        $firstItemType = $schema->itemTypes[0];
        $this->assertIsString($firstItemType->itemType);
        $this->assertContainsOnlyInstancesOf(
            \Arete\Logos\Models\Zotero\Field::class,
            $firstItemType->fields
        );
        $this->assertContainsOnlyInstancesOf(
            \Arete\Logos\Models\Zotero\CreatorType::class,
            $firstItemType->creatorTypes
        );
        return $schema;
    }

    /**
     * @depends testSchemaTypeHaveExpectedDataStrcture
     *
     * @param Schema $schema
     *
     * @return Schema
     */
    public function testSchemaCslMapHaveExpectedDataStructure(Schema $schema): Schema
    {
        $cslMap = $schema->csl;
        $this->assertInstanceOf(CSLMap::class, $cslMap);

        $this->assertObjectHasAttribute('types', $cslMap);
        $this->assertObjectHasAttribute('fields', $cslMap);
        $this->assertObjectHasAttribute('names', $cslMap);

        return $schema;
    }

    /**
     * @depends testSchemaCslMapHaveExpectedDataStructure
     *
     * @param Schema $schema
     *
     * @return Schema
     */
    public function testSchemaMetaHaveBasicDataTypes(Schema $schema): Schema
    {
        $meta = $schema->meta;
        $this->assertArrayHasKey('fields', $meta);
        $this->assertArrayHasKey('type', $meta['fields']['date']);
        return $schema;
    }

    /**
     * @depends testSchemaMetaHaveBasicDataTypes
     *
     * @param mixed $schema
     *
     * @return [type]
     */
    public function testSchemaHaveSomeData(Schema $schema): Schema
    {
        // $this->log($schema);
        $itemTypes = $schema->itemTypes;
        $expectedTypes = ['annotation', 'blogPost', 'book', 'bookSection', 'journalArticle'];
        // print_r($itemTypes);
        foreach ($expectedTypes as $expType) {
            $matches = array_filter($itemTypes, function ($type) use ($expType) {
                return $type->itemType == $expType;
            });
            $this->assertGreaterThan(0, count($matches));
        }
        return $schema;
    }
}
