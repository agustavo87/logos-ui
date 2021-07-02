<?php

declare(strict_types=1);

namespace Arete\Logos\Application;

use Arete\Common\Provider;
use Arete\Logos\Application\ValueTypeMapper;
use Arete\Logos\Application\SourceTypeLabelsMap;
use Arete\Logos\Application\Zotero\ZoteroSchemaLoader;
use Arete\Logos\Application\Ports\Interfaces\ZoteroSchemaLoaderInterface;
use Arete\Logos\Application\Ports\Abstracts\ConfigurationRepository;
use Arete\Logos\Application\Ports\Interfaces\ComplexSourcesRepository;

class SourcesProvider extends Provider
{
    public function register()
    {
        $this->container::register(
            \Arete\Logos\Application\Abstracts\ValueTypeMapper::class,
            function ($container) {
                return new ValueTypeMapper(
                    $container::get(ConfigurationRepository::class)
                );
            }
        );

        $this->container::register(
            \Arete\Logos\Application\Abstracts\MapsSourceTypeLabels::class,
            function ($container) {
                return new SourceTypeLabelsMap(
                    $container::get(ConfigurationRepository::class)
                );
            }
        );

        $this->container::register(
            ZoteroSchemaLoaderInterface::class,
            function ($container) {
                return new ZoteroSchemaLoader();
            }
        );

        $this->container::register(
            \Arete\Logos\Domain\Schema::class,
            function ($container) {
                // in the fuutre could need some inyection of external data source.
                return new \Arete\Logos\Domain\Schema();
            }
        );

        $this->container::register(
            \Arete\Logos\Application\Ports\Interfaces\FilteredIndexUseCase::class,
            function ($container) {
                return new \Arete\Logos\Application\FilteredIndexUseCase(
                    $container::get(ComplexSourcesRepository::class)
                );
            }
        );
    }
}
