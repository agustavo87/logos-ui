<?php

declare(strict_types=1);

namespace Arete\Logos\Ports;

use Arete\Logos\Services\LogosContainer;

class Logos
{
    public static function __callStatic($name, $arguments)
    {
        return LogosContainer::$name(...$arguments);
    }
}
