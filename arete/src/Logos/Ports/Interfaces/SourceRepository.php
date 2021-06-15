<?php

declare(strict_types=1);

namespace Arete\Logos\Ports\Interfaces;

use Arete\Logos\Models\Source;

interface SourceRepository
{
    public function createFromArray(array $params): Source;
}