<?php

declare(strict_types=1);

namespace Arete\Logos\Application\Ports\Interfaces;

interface ComplexSourcesRepository extends SourcesRepository
{
    public function complexFilter(array $params): array;
}
