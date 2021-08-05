<?php

declare(strict_types=1);

namespace Arete\Logos\Domain;

use Arete\Logos\Domain\Abstracts\Attributable;
use Arete\Logos\Application\Ports\Interfaces\SourceTypeRepository;
use Arete\Logos\Domain\Contracts\Source as SourceContract;
use Arete\Logos\Domain\Abstracts\SourceType;
use Arete\Logos\Domain\Contracts\Formatter;

class Source extends Attributable implements SourceContract
{
    protected ?string $genus = 'source';
    protected ParticipationSet $participations;
    protected Formatter $formater;
    protected string $key;

    /** @todo find all references to constructor an update */
    public function __construct(
        SourceTypeRepository $types,
        Formatter $formater,
        array $properties = []
    ) {
        parent::__construct($types, $properties);
        $this->formater = $formater;
    }

    public function participations(): ParticipationSet
    {
        return $this->participations;
    }

    public function type(): SourceType
    {
        return $this->type ?? ($this->type = $this->types->get($this->typeCode));
    }

    public function render($params = null): string
    {
        return $this->formater->format($this);
    }

    public function setFormatter(Formatter $fomater)
    {
        $this->formater = $fomater;
    }

    public function key(): string
    {
        return $this->key;
    }

    /**
     *  Returns an array representation of the source
     *
     * @param string    $participationsOrderBy 'id' | 'relevance' | other;
     *                                      - 'id': key participations by id.
     *                                      - 'relevance': order participations by relevance.
     *                                      - other: no order.
     *
     * @return array
     */
    public function toArray(?string $participationsOrderBy = 'id'): array
    {
        $sourceInfo = parent::toArray();
        $sourceInfo['key'] = $this->key();
        $sourceInfo['participations'] = $this->participations->toArray($participationsOrderBy);
        return $sourceInfo;
    }
}
