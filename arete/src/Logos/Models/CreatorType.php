<?php

declare(strict_types=1);

namespace Arete\Logos\Models;

use Arete\Logos\Models\Abstracts\Type;

abstract class CreatorType extends Type
{
    protected ?string $genus = 'creator';
}
