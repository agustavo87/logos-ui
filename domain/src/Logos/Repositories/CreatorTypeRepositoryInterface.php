<?php

declare(strict_types=1);

namespace Arete\Logos\Repositories;

use Arete\Logos\Models\CreatorType;

interface CreatorTypeRepositoryInterface
{
    public function get($codeName): CreatorType;
}
