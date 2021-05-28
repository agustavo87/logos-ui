<?php

declare(strict_types=1);

namespace Arete\Logos\Tests;

use PHPUnit\Framework\TestCase;
use Arete\Logos\Tests\Traits\ChecksZoteroSchemaDataStructure;
use Arete\Logos\Models\Zotero\{Schema, Field, CreatorType, CSLMap};

class SimpleZoteroSchemaLoaderTest extends TestCase
{
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
}
