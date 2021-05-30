<?php

declare(strict_types=1);

namespace Tests\Feature\Logos;

use Tests\FixturableTestCase;

class ZoteroLogosMapper extends FixturableTestCase
{
    public function testZoteroLogosMapperMaps()
    {
        $mapper = $this->app->make(\Arete\Logos\Services\Zotero\LogosMapper::class);
        $this->assertEquals('date', $mapper->mapValueType('date'));
        $this->assertEquals('text', $mapper->mapValueType('no-exist'));
    }
}