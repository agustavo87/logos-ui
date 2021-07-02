<?php

declare(strict_types=1);

namespace Arete\Logos\Application\Ports\Interfaces;

interface FilteredIndexUseCase
{
    public function filter(array $params): array;
}
