<?php

declare(strict_types=1);

namespace Arete\Logos\Models;

use Arete\Logos\Models\Traits\ExposeAttributes;
use Arete\Common\Interfaces\Arrayable;
use Arete\Common\FillsProperties;

class Source implements SourceInterface, Arrayable
{
    use ExposeAttributes;
    use FillsProperties;

    protected int $id;
    protected SourceType $type;
    protected ParticipationSet $participations;

    public function id(): int
    {
        return $this->id;
    }

    public function type(): SourceType
    {
        return $this->type;
    }

    public function participations(): ParticipationSet
    {
        return $this->participations;
    }

    public function pushAttribute($code, $value)
    {
        $this->attributes[$code] = $value;
    }

    public function toArray()
    {
        return $this->attributes;
    }
}
