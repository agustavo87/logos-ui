<?php

declare(strict_types=1);

namespace Arete\Logos\Application;

use Arete\Common\Provider;
use Arete\Logos\Application\Abstracts\MapsSourceTypeLabels;
use Arete\Logos\Application\Abstracts\ValueTypeMapper;
use Arete\Logos\Application\Ports\Abstracts\ConfigurationRepository;
use Arete\Logos\Application\Ports\Interfaces\CreatorsRepository;
use Arete\Logos\Application\Ports\Interfaces\CreatorTypeRepository;
use Arete\Logos\Application\Ports\Interfaces\LogosEnviroment;
use Arete\Logos\Application\Ports\Interfaces\ParticipationRepository;
use Arete\Logos\Application\Ports\Interfaces\SourceTypeRepository;
use Arete\Logos\Application\Ports\Interfaces\ZoteroSchemaLoaderInterface;
use Arete\Logos\Infrastructure\Defaults\ConfigurationRepository as DefaultConfigurationRepository;
use Arete\Logos\Infrastructure\Defaults\CreatorTypeRepository as DefaultCreatorTypeRepository;
use Arete\Logos\Infrastructure\Defaults\LogosEnviroment as DefaultLogosEnviroment;
use Arete\Logos\Infrastructure\Defaults\MemoryCreatorRepository;
use Arete\Logos\Infrastructure\Defaults\MemoryParticipationRepository;
use Arete\Logos\Infrastructure\Defaults\ZoteroSourceTypeRepository;
use Arete\Logos\Domain\Schema;

class TestSourcesProvider extends Provider
{
    public function register()
    {
        $this->container::register(
            ConfigurationRepository::class,
            function ($container) {
                return new DefaultConfigurationRepository();
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

        $this->container::register(
            LogosEnviroment::class,
            function ($container) {
                return new DefaultLogosEnviroment(
                    $container::get(ConfigurationRepository::class)
                );
            }
        );

        $this->container::register(
            CreatorsRepository::class,
            function ($container) {
                return new MemoryCreatorRepository(
                    $container::get(CreatorTypeRepository::class),
                    $container::get(LogosEnviroment::class)
                );
            }
        );
    }
}
