<?php

declare(strict_types=1);

namespace Tests\Feature\Logos;

use Tests\TestCase;
use Arete\Logos\Interfaces\ValueTypeMapper;

class ValueTypeMapperTest extends TestCase
{
    public function testLogosValueTypeMapperMaps()
    {
        $mapper = $this->app->make(ValueTypeMapper::class);
        $this->assertEquals('date', $mapper->mapValueType('date'));
        $this->assertEquals('text', $mapper->mapValueType('no-exist'));
    }
}
