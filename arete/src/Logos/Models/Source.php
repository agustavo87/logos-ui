<?php

declare(strict_types=1);

namespace Arete\Logos\Models;

use Arete\Logos\Models\Traits\ExposeAttributes;
use Arete\Common\Interfaces\Arrayable;
use Arete\Common\FillsProperties;
use Arete\Logos\Ports\Interfaces\SourceTypeRepository;

class Source implements SourceInterface, Arrayable
{
    use ExposeAttributes;
    use FillsProperties;

    protected int $id;
    protected string $typeCode;
    protected ?SourceType $type = null;
    protected SourceTypeRepository $sourceTypes;
    protected ParticipationSet $participations;

    public function __construct(
        SourceTypeRepository $sourceTypes,
        array $properties = []
    ) {
        $this->sourceTypes = $sourceTypes;
        $this->fill($properties);
    }

    public function id(): int
    {
        return $this->id;
    }

    public function type(): SourceType
    {
        return $this->type ?? ($this->type = $this->sourceTypes->get($this->typeCode));
    }

    public function typeCode(): string
    {
        return $this->typeCode;
    }

    public function participations(): ParticipationSet
    {
        return $this->participations;
    }
}
