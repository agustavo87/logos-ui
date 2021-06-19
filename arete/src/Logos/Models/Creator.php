<?php

declare(strict_types=1);

namespace Arete\Logos\Models;

use Arete\Logos\Models\Abstracts\Attributable;
use Arete\Logos\Ports\Interfaces\CreatorTypeRepository;

class Creator extends Attributable
{
    protected ?string $genus = 'creator';

    public function __construct(
        CreatorTypeRepository $types,
        array $properties = [],
        array $attributes = []
    ) {
        parent::__construct($types, $properties);
        $this->attributes = $attributes;
    }
}
