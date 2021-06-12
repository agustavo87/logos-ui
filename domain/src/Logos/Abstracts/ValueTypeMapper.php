<?php

declare(strict_types=1);

namespace Arete\Logos\Abstracts;

use Arete\Logos\Interfaces\ValueTypeMapper as ValueTypeMapperInterface;

abstract class ValueTypeMapper implements ValueTypeMapperInterface
{
    abstract public function mapValueType(string $codeName): ?string;

    public function __invoke(string $codeName)
    {
        return $this->mapValueType($codeName);
    }
}
