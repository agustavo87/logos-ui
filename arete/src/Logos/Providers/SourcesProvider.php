<?php

declare(strict_types=1);

namespace Arete\Logos\Providers;

use Arete\Common\Provider;
use Arete\Logos\Services\ValueTypeMapper;
use Arete\Logos\Services\SourceTypeLabelsMap;
use Arete\Logos\Services\Zotero\ZoteroSchemaLoader;
use Arete\Logos\Ports\Interfaces\ZoteroSchemaLoaderInterface;
use Arete\Logos\Ports\Abstracts\ConfigurationRepository;

class SourcesProvider extends Provider
{
    public function register()
    {
        $this->container::register(
            \Arete\Logos\Abstracts\ValueTypeMapper::class,
            function ($container) {
                return new ValueTypeMapper(
                    $container::get(ConfigurationRepository::class)
                );
            }
        );

        $this->container::register(
            \Arete\Logos\Abstracts\MapsSourceTypeLabels::class,
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
    }
}
