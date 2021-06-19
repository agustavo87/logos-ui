<?php

declare(strict_types=1);

namespace Arete\Logos\Models;

use Arete\Logos\Models\Abstracts\Attributable;
use Arete\Logos\Ports\Interfaces\SourceTypeRepository;

class Source extends Attributable implements SourceInterface
{
    protected ?string $genus = 'source';
    protected ParticipationSet $participations;

    public function __construct(
        SourceTypeRepository $types,
        array $properties = []
    ) {
        parent::__construct($types, $properties);
    }

    public function participations(): ParticipationSet
    {
        return $this->participations;
    }
}
