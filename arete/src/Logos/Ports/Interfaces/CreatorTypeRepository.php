<?php

declare(strict_types=1);

namespace Arete\Logos\Ports\Interfaces;

use Arete\Logos\Models\CreatorType;

interface CreatorTypeRepository
{
    public function get($codeName): CreatorType;
}
