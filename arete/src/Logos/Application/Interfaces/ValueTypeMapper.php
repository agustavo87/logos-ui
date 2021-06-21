<?php

declare(strict_types=1);

namespace Arete\Logos\Application\Interfaces;

interface ValueTypeMapper
{
    public function mapValueType(string $codeName): ?string;
}
