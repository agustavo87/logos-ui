<?php

declare(strict_types=1);

namespace Arete\Logos\Models;

use Arete\Logos\Models\Traits\ExposeAttributes;
use Arete\Logos\Models\SourceInterface;

class ParticipationSet
{
    use ExposeAttributes;

    protected SourceInterface $source;

    public function __construct(SourceInterface $source)
    {
        $this->source = $source;
    }

    public function push(string $role, ParticipationInterface $participation)
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

    public function source(): SourceInterface
    {
        return $this->source;
    }
}
