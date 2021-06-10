<?php

declare(strict_types=1);

namespace Arete\Logos\Ports\Interfaces;

use Arete\Logos\Models\SourceType;

interface SourceTypeRepository
{
    public function get($codeName): SourceType;
}
