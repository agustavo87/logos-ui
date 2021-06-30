<?php

declare(strict_types=1);

namespace Arete\Logos\Application;

use Arete\Common\Provider;
use Arete\Logos\Application\Abstracts\MapsSourceTypeLabels;
use Arete\Logos\Application\Abstracts\ValueTypeMapper;
use Arete\Logos\Application\Ports\Abstracts\ConfigurationRepository as ConfigurationRepositoryPort;
use Arete\Logos\Application\Ports\Interfaces\SourceTypeRepository;
use Arete\Logos\Application\Ports\Interfaces\ZoteroSchemaLoaderInterface;
use Arete\Logos\Infrastructure\Mocks\ZoteroSourceTypeRepository;

class TestSourcesProvider extends Provider
{
    public function register()
    {
        $this->container::register(
            ConfigurationRepositoryPort::class,
            function ($container) {
                return new \Arete\Logos\Infrastructure\Mocks\ConfigurationRepository();
            }
        );

        $this->container::register(
            SourceTypeRepository::class,
            function ($container) {
                return new ZoteroSourceTypeRepository(
                    $container::get(ValueTypeMapper::class),
                    $container::get(MapsSourceTypeLabels::class),
                    $container::get(ZoteroSchemaLoaderInterface::class)
                );
            }
        );
    }
}
