<?php

declare(strict_types=1);

namespace Tests\Feature\Arete;

use Tests\FixturableTestCase;

class AreteEnvironmentTest extends FixturableTestCase
{
    public function testConfigValuesAreAccesibles()
    {
        $this->assertIsString(config('sources.usersTable'));
        $this->assertIsString(config('sources.usersPK'));
    }
}
