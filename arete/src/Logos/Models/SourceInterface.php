<?php

declare(strict_types=1);

namespace Arete\Logos\Models;

interface SourceInterface
{
    public function participations(): ParticipationSet;
}
