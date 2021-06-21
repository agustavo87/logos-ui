<?php

declare(strict_types=1);

namespace Arete\Logos\Application\Abstracts;

use Arete\Logos\Application\Interfaces\ValueTypeMapper as ValueTypeMapperInterface;

abstract class ValueTypeMapper implements ValueTypeMapperInterface
{
    abstract public function mapValueType(string $codeName): ?string;

    public function __invoke(string $codeName)
    {
        return $this->mapValueType($codeName);
    }
}
