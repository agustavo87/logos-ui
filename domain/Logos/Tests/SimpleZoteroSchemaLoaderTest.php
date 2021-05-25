<?php

declare(strict_types=1);

namespace Arete\Logos\Tests;

use Arete\Logos\Tests\Traits\ChecksZoteroSchemaDataStructure;
use PHPUnit\Framework\TestCase;

class SimpleZoteroSchemaLoaderTest extends TestCase
{
    use ChecksZoteroSchemaDataStructure;

    public function testSchemaHaveExpectedDataStructure()
    {
        $schemaLoader = new \Arete\Logos\Services\Zotero\SimpleSchemaLoader();
        $this->isInstanceOf(\Arete\Logos\Services\Zotero\SchemaLoaderInterface::class, $schemaLoader);

        $schema = $schemaLoader->load();
        $this->checkSchemaDataStructure($schema);
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
    }
}
