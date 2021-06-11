<?php

declare(strict_types=1);

namespace Arete\Logos\Adapters\Laravel;

use Arete\Logos\Ports\Interfaces\ConfigurationRepository as ConfigurationRepositoryPort;
use Illuminate\Config\Repository as LaravelConfigRepository;

class LvConfigurationRepository implements ConfigurationRepositoryPort
{
    protected array $config;

    public function __construct(LaravelConfigRepository $config)
    {
        $this->config = $config->get('sources');
    }

    public function get(string $key)
    {
        return $this->config[$key] ?? null;
    }
}
