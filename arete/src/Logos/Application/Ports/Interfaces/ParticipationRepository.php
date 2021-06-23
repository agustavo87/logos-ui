<?php

declare(strict_types=1);

namespace Arete\Logos\Application\Ports\Interfaces;

use Arete\Logos\Domain\Contracts\Participation;
use Arete\Logos\Domain\Creator;
use Arete\Logos\Domain\Source;

interface ParticipationRepository
{
    /**
     * @param Source $source
     * @param Creator $creator
     * @param string $role
     * @param int $relevance
     *
     * @return void
     */
    public function create(Source $source, Creator $creator, string $role, int $relevance): Participation;
}
