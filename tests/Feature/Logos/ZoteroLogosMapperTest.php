<?php

declare(strict_types=1);

namespace Tests\Feature\Logos;

use Tests\FixturableTestCase;

class ZoteroLogosMapperTest extends FixturableTestCase
{
    public function testZoteroLogosMapperMaps()
    {
        $mapper = \Arete\Logos\Ports\Logos::valueTypes();
        $this->assertEquals('date', $mapper->mapValueType('date'));
        $this->assertEquals('text', $mapper->mapValueType('no-exist'));
    }
}
