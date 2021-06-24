<?php

declare(strict_types=1);

namespace Arete\Logos\Domain;

use Arete\Logos\Domain\Traits\ExposeAttributes;
use Arete\Logos\Domain\Contracts\Participation;
use Arete\Logos\Domain\Abstracts\CreatorType;
use Arete\Logos\Domain\Source;

class SimpleParticipation implements Participation
{
    use ExposeAttributes;

    protected int $relevance;
    protected ?Creator $creator = null;
    protected Role $role;
    protected Source $source;

    public function __construct(
        Source $source,
        Role $role,
        Creator $creator,
        int $relevance
    ) {
        $this->source = $source;
        $this->role = $role;
        $this->creator = $creator;
        // $this->attributes = $creator->toArray();
        $this->role = $role;
        $this->relevance = $relevance;
    }

    public function __get($name)
    {
        return $this->creator->$name;
    }

    public function __set($name, $value)
    {
        return $this->creator->$name = $value;
    }

    public function creatorId(): int
    {
        return $this->creator->id();
    }

    public function creator(): Creator
    {
        return $this->creator;
    }

    public function creatorType(): CreatorType
    {
        return $this->creator->type();
    }

    public function role(): Role
    {
        return $this->role;
    }

    public function relevance(): int
    {
        return $this->relevance;
    }

    public function source(): Source
    {
        return $this->source;
    }

    public function setRelevance(int $relevance): Participation
    {
        $this->relevance = $relevance;
        return $this;
    }
}
