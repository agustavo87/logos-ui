<?php

declare(strict_types=1);

namespace Arete\Logos\Ports\Interfaces;

use Arete\Logos\Interfaces\TypeRepository;
use Arete\Logos\Models\SourceType;

interface SourceTypeRepository extends TypeRepository
{
    public function get($codeName): SourceType;
}
