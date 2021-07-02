<?php

declare(strict_types=1);

namespace Arete\Logos\Application;

use Arete\Common\Container;

class LogosContainer extends Container
{
    /**
     * As convention, the alias indicate a service that can be
     * accesed externally, normally by an adapter.
     */
    protected static array $alias = [
        'config' => \Arete\Logos\Application\Ports\Abstracts\ConfigurationRepository::class,
        'valueTypes' => \Arete\Logos\Application\Abstracts\ValueTypeMapper::class,
        'sourceTypeLabels' => \Arete\Logos\Application\Abstracts\MapsSourceTypeLabels::class,
        'zoteroSchema' => \Arete\Logos\Application\Ports\Interfaces\ZoteroSchemaLoaderInterface::class,
        'schema' => \Arete\Logos\Domain\Schema::class,
        'filteredIndex' => \Arete\Logos\Application\Ports\Interfaces\FilteredIndexUseCase::class
    ];

    protected static array $providers = [
        \Arete\Logos\Application\SourcesProvider::class,
    ];
}
