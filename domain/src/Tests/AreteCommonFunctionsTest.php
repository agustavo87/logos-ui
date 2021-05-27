<?php

declare(strict_types=1);

namespace Arete\Tests;

use PHPUnit\Framework\TestCase;

class AreteCommonFunctionsTest extends TestCase
{
    public function testFunctionsAreAccesibles()
    {
        $this->assertTrue(\Arete\Common\testFunction());
    }

    public function testArrayWhiteList()
    {
        $this->assertEquals(
            ['a' => 1, 'd' => 4],
            \Arete\Common\array_filter_keys(
                ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4],
                ['a','d']
            )
        );
    }
}
