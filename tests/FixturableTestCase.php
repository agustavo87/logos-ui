<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class FixturableTestCase extends BaseTestCase
{
    use FixturesTests;
    use LogsInformation;
}
