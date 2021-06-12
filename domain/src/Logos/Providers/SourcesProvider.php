<?php

declare(strict_types=1);

namespace Arete\Logos\Providers;

use Arete\Common\Provider;
use Arete\Logos\Services\ValueTypeMapper;
use Arete\Logos\Services\SourceTypeLabelsMap;

class SourcesProvider extends Provider
{
    public function register()
    {
        $this->container::register(
            \Arete\Logos\Abstracts\ValueTypeMapper::class,
            function ($container) {
                return new ValueTypeMapper();
            }
        );

        $this->container::register(
            \Arete\Logos\Abstracts\MapsSourceTypeLabels::class,
            function ($container) {
                return new SourceTypeLabelsMap();
            }
        );
    }
}
