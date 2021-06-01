<?php

declare(strict_types=1);

namespace Arete\Logos\Repositories;

use Arete\Logos\Models\SourceType;

interface SourceTypeRepositoryInterface
{
    public function get($codeName): SourceType;
}
