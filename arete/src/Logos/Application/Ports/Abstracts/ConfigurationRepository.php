<?php

declare(strict_types=1);

namespace Arete\Logos\Application\Ports\Abstracts;

abstract class ConfigurationRepository
{
    /**
     * @param string $key
     *
     * @return null|string|array
     */
    abstract public function get(string $key);

    public function __invoke(string $key)
    {
        return $this->get($key);
    }
}
