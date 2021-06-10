<?php

declare(strict_types=1);

namespace Arete\Logos\Models;

interface SourceInterface
{
    public function id(): int;
    public function type(): SourceType;
    public function participations(): ParticipationSet;
}
