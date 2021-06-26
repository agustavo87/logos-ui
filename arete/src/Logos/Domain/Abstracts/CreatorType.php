<?php

declare(strict_types=1);

namespace Arete\Logos\Domain\Abstracts;

abstract class CreatorType extends Type
{
    protected ?string $genus = 'creator';
}
