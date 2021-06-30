<?php

declare(strict_types=1);

namespace Arete\Logos\Application;

use Arete\Common\Provider;
use Arete\Logos\Application\Abstracts\MapsSourceTypeLabels;
use Arete\Logos\Application\Abstracts\ValueTypeMapper;
use Arete\Logos\Application\Ports\Abstracts\ConfigurationRepository as ConfigurationRepositoryPort;
use Arete\Logos\Application\Ports\Interfaces\CreatorTypeRepository;
use Arete\Logos\Application\Ports\Interfaces\ParticipationRepository;
use Arete\Logos\Application\Ports\Interfaces\SourceTypeRepository;
use Arete\Logos\Application\Ports\Interfaces\ZoteroSchemaLoaderInterface;
use Arete\Logos\Domain\Schema;
use Arete\Logos\Infrastructure\Defaults\CreatorTypeRepository as DefaultCreatorTypeRepository;
use Arete\Logos\Infrastructure\Defaults\MemoryParticipationRepository;
use Arete\Logos\Infrastructure\Defaults\ZoteroSourceTypeRepository;

class TestSourcesProvider extends Provider
{
    public function register()
    {
        $this->container::register(
            ConfigurationRepositoryPort::class,
            function ($container) {
                return new \Arete\Logos\Infrastructure\Defaults\ConfigurationRepository();
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

        $this->container::register(
            CreatorTypeRepository::class,
            function ($container) {
                return new DefaultCreatorTypeRepository(
                    $container::get(Schema::class),
                    $container::get(ValueTypeMapper::class)
                );
            }
        );

        $this->container::register(
            ParticipationRepository::class,
            function ($container) {
                return new MemoryParticipationRepository();
            }
        );
    }
}
