<?php

declare(strict_types=1);

namespace Arete\Logos\Factories;

use Arete\Logos\Models\SimpleParticipation;
use Arete\Logos\Models\ParticipationInterface;

class Participation
{
    public static function __callStatic($name, $arguments)
    {
        return (new static())->$name(...$arguments);
    }

    protected function fromArray($params): ParticipationInterface
    {
        return new SimpleParticipation($params);
    }
}
