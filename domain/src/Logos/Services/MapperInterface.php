<?php

declare(strict_types=1);

namespace Arete\Logos\Services;

interface MapperInterface
{
    public function mapValueType(string $codeName): ?string;
}
