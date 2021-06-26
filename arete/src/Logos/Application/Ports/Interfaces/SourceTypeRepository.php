<?php

declare(strict_types=1);

namespace Arete\Logos\Application\Ports\Interfaces;

use Arete\Logos\Domain\Contracts\TypeRepository;
use Arete\Logos\Domain\Abstracts\SourceType;

interface SourceTypeRepository extends TypeRepository
{
    public function get($codeName): SourceType;
}
