<?php

declare(strict_types=1);

namespace Arete\Logos\Tests\Traits;

trait ChecksZoteroSchemaDataStructure
{
    public function checkSchemaDataStructure($schema)
    {
        $this->assertInstanceOf(\Arete\Logos\Models\Zotero\Schema::class, $schema);
        $this->assertIsInt($schema->version);
        $this->assertContainsOnlyInstancesOf(
            \Arete\Logos\Models\Zotero\ItemType::class,
            $schema->itemTypes
        );
        $this->assertIsArray($schema->meta);
        $this->assertInstanceOf(\Arete\Logos\Models\Zotero\CSLMap::class, $schema->csl);
    }
}
