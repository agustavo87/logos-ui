<?php

declare(strict_types=1);

namespace Arete\Logos\Ports\Interfaces;

use Arete\Logos\Interfaces\TypeRepository;
use Arete\Logos\Models\CreatorType;

interface CreatorTypeRepository extends TypeRepository
{
    public function get($codeName): CreatorType;
}
