<?php

declare(strict_types=1);

namespace Arete\Logos\Tests;

use PHPUnit\Framework\TestCase;

class ZoteroLogosMapper extends TestCase
{
    public function testZoteroLogosMapperMaps()
    {
        $mapper = new \Arete\Logos\Services\Zotero\LogosMapper([
            'default' => 'text',
            'date'    => 'date'
        ]);
        $this->assertEquals('text', $mapper->mapValueType('default'));
        $this->assertEquals('date', $mapper->mapValueType('date'));
        $this->assertNull($mapper->mapValueType('no-exist'));
    }
}
