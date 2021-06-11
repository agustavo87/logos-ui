<?php

declare(strict_types=1);

namespace Arete\Logos\Ports\Interfaces;

interface ConfigurationRepository
{
    /**
     * @param string $key
     *
     * @return null|string|array
     */
    public function get(string $key);
}
