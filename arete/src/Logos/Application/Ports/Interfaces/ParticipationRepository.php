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
    public function create(
        Source $source,
        Creator $creator,
        string $role,
        int $relevance
    ): Participation;

    public function remove(
        Source $source,
        string $roleCode,
        $creatorID
    ): bool;

    /**
     * Loads participations from persistence
     *
     * @param Source $source
     *
     * @return Participation[]
     */
    public function load(Source $source): array;

    /**
     * @param Participation[]|Participation $participations
     *
     * @return void
     */
    public function save($participations): int;
}
