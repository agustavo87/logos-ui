<?php

declare(strict_types=1);

namespace Arete\Logos\Application\Ports\Interfaces;

use Arete\Logos\Domain\Contracts\TypeRepository;
use Arete\Logos\Domain\Abstracts\CreatorType;

interface CreatorTypeRepository extends TypeRepository
{
    public function get($codeName): CreatorType;
}
