<?php

declare(strict_types=1);

namespace Arete\Logos\Domain;

use Arete\Logos\Domain\Abstracts\Attributable;
use Arete\Logos\Application\Ports\Interfaces\CreatorTypeRepository;
use Arete\Logos\Domain\Contracts\Ownerable;

class Creator extends Attributable implements Ownerable
{
    protected ?string $genus = 'creator';
    protected $ownerID;

    public function __construct(
        CreatorTypeRepository $types,
        array $properties = [],
        array $attributes = []
    ) {
        parent::__construct($types, $properties);
        $this->attributes = $attributes;
    }

    public function ownerID()
    {
        return $this->ownerID;
    }
}
