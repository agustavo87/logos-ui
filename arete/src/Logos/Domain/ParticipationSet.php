<?php

declare(strict_types=1);

namespace Arete\Logos\Domain;

use Arete\Logos\Domain\Traits\ExposeAttributes;
use Arete\Logos\Domain\Contracts\Source;
use Arete\Logos\Domain\Contracts\Participation;

class ParticipationSet
{
    use ExposeAttributes;

    protected Source $source;

    public function __construct(Source $source)
    {
        $this->source = $source;
    }

    public function push(string $role, Participation $participation)
    {
        if (!array_key_exists($role, $this->attributes)) {
            $this->attributes[$role] = [];
        }
        $this->attributes[$role][$participation->creatorId()] = $participation;
    }

    /**
     * @return string[]
     */
    public function roles(): array
    {
        return $this->attributes();
    }

    public function source(): Source
    {
        return $this->source;
    }
}
