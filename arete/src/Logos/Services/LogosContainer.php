<?php

declare(strict_types=1);

namespace Arete\Logos\Services;

use Arete\Common\Container;

class LogosContainer extends Container
{
    protected static array $alias = [
        'greet' => 'example',
        'greeter' => 'object',
        'config' => \Arete\Logos\Ports\Abstracts\ConfigurationRepository::class,
        'valueTypes' => \Arete\Logos\Abstracts\ValueTypeMapper::class,
        'sourceTypeLabels' => \Arete\Logos\Abstracts\MapsSourceTypeLabels::class,
        'zoteroSchema' => \Arete\Logos\Ports\Interfaces\ZoteroSchemaLoaderInterface::class,
        'schema' => \Arete\Logos\Models\Schema::class
    ];

    protected static array $providers = [
        \Arete\Logos\Providers\ExampleProvider::class,
        \Arete\Logos\Providers\SourcesProvider::class
    ];
}
