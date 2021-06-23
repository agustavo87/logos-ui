<?php

declare(strict_types=1);

namespace Arete\Logos\Domain;

use Arete\Logos\Domain\Abstracts\Attributable;
use Arete\Logos\Application\Ports\Interfaces\SourceTypeRepository;
use Arete\Logos\Domain\Contracts\Source as SourceContract;
use Arete\Logos\Domain\Abstracts\SourceType;

class Source extends Attributable implements SourceContract
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

    public function type(): SourceType
    {
        return $this->type ?? ($this->type = $this->types->get($this->typeCode));
    }
}
