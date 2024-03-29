<?php

declare(strict_types=1);

namespace Arete\Logos\Domain\Contracts;

use Arete\Logos\Domain\ParticipationSet;

interface Source extends Ownerable
{
    public function participations(): ParticipationSet;

    public function render(array $params = null): string;

    public function setFormatter(Formatter $fomater);

    public function key(): string;
}
