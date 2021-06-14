<?php

declare(strict_types=1);

namespace Arete\Logos\Services;

use Arete\Logos\Abstracts\MapsSourceTypeLabels;
use Arete\Logos\Services\LogosContainer as Logos;

/**
 * Maps Source Types Codes to Labels.
 */
class SourceTypeLabelsMap extends MapsSourceTypeLabels
{
    public function __construct()
    {
        $typeLabels = Logos::config('source')['typesLabels'];
        $this->default = $typeLabels['default'];
        $this->map = $typeLabels['map'];
        return true;
    }
}
