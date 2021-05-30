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
        $this->assertEquals('date', $mapper->mapValueType('date'));
        $this->assertEquals('text', $mapper->mapValueType('no-exist')); // default
        $this->assertTrue($mapper->has('date'));
        $this->assertFalse($mapper->has('number'));
    }
}
