<?php

declare(strict_types=1);

namespace Tests\Arete;

use Tests\FixturableTestCase;

class AreteEnvironmentTest extends FixturableTestCase
{
    public function testConfigValuesAreAccesibles()
    {
        $this->assertIsString(config('logos.usersTable'));
        $this->assertIsString(config('logos.usersPK'));
    }
}
