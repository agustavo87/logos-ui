<?php

declare(strict_types=1);

namespace Arete\Logos\Ports;

use Arete\Logos\Services\LogosContainer;

/**
 * Logos facade
 *
 * Allows to access services vía container aliases
 */
class Logos
{
    public static function __callStatic($name, $arguments)
    {
        return LogosContainer::$name(...$arguments);
    }
}
