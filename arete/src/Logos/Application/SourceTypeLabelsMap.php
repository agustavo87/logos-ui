<?php

declare(strict_types=1);

namespace Arete\Logos\Application;

use Arete\Logos\Application\Abstracts\MapsSourceTypeLabels;
use Arete\Logos\Application\Ports\Abstracts\ConfigurationRepository;

/**
 * Maps Source Types Codes to Labels.
 */
class SourceTypeLabelsMap extends MapsSourceTypeLabels
{
    public function __construct(ConfigurationRepository $config)
    {
        $typeLabels = $config('source')['typesLabels'];
        $this->default = $typeLabels['default'];
        $this->map = $typeLabels['map'];
        return true;
    }
}
