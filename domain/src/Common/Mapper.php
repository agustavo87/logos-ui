<?php

declare(strict_types=1);

namespace Arete\Common;

abstract class Mapper
{
    public array $map;

    public $default;

    public function __construct(?array $bootData)
    {
        $this->boot($bootData);
    }

    abstract protected function boot(array $data): bool;

    public function map($x)
    {
        return $this->map[$x] ?? $this->default;
    }

    public function has($x): bool
    {
        return array_key_exists($x, $this->map);
    }
}
