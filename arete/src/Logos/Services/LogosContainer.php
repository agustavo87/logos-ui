<?php

declare(strict_types=1);

namespace Arete\Logos\Services;

use Arete\Common\Container;

class LogosContainer extends Container
{
    /**
     * As convention, the alias indicate a service that can be
     * accesed externally, normally by an adapter.
     */
    protected static array $alias = [
        'config' => \Arete\Logos\Ports\Abstracts\ConfigurationRepository::class,
        'valueTypes' => \Arete\Logos\Abstracts\ValueTypeMapper::class,
        'sourceTypeLabels' => \Arete\Logos\Abstracts\MapsSourceTypeLabels::class,
        'zoteroSchema' => \Arete\Logos\Ports\Interfaces\ZoteroSchemaLoaderInterface::class,
        'schema' => \Arete\Logos\Domain\Schema::class
    ];

    protected static array $providers = [
        \Arete\Logos\Providers\SourcesProvider::class
    ];
}
