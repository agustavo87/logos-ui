<?php

declare(strict_types=1);

namespace Arete\Logos\Tests;

use Arete\Logos\Models\Zotero\Schema;
use Arete\Logos\Tests\Traits\ChecksZoteroSchemaDataStructure;
use PHPUnit\Framework\TestCase;

class ZoteroSchemaTest extends TestCase
{
    use ChecksZoteroSchemaDataStructure;

    public function testSchemaCreatesWithDefaults()
    {
        $schema = new Schema();
        $this->checkSchemaDataStructure($schema);
    }

    public function testSchemaMergesDefaults()
    {
        $schema = new Schema(['version' => 2]);
        $this->checkSchemaDataStructure($schema);
        $this->assertEquals(2, $schema->version);
    }
}
