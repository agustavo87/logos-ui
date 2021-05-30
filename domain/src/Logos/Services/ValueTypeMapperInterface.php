<?php

declare(strict_types=1);

namespace Arete\Logos\Services;

interface ValueTypeMapperInterface
{
    public function mapValueType(string $codeName): ?string;
}
