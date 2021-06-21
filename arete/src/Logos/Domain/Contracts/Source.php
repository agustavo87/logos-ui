<?php

declare(strict_types=1);

namespace Arete\Logos\Domain\Contracts;

use Arete\Logos\Domain\ParticipationSet;

interface Source
{
    public function participations(): ParticipationSet;
}
