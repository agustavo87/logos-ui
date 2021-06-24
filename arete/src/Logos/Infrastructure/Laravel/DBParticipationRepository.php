<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Laravel;

use Arete\Logos\Infrastructure\Laravel\Common\DBRepository;
use Arete\Logos\Application\Ports\Interfaces\ParticipationRepository;
use Arete\Logos\Domain\Contracts\Participation;
use Arete\Logos\Domain\Creator;
use Arete\Logos\Domain\SimpleParticipation;
use Arete\Logos\Domain\Source;

class DBParticipationRepository extends DBRepository implements ParticipationRepository
{
    public function create(Source $source, Creator $creator, string $role, int $relevance): Participation
    {
        $roleObj = $source->type()->participations()->$role;

        $this->db->insertParticipation(
            $source,
            $creator,
            $roleObj,
            $relevance
        );

        $participation = new SimpleParticipation(
            $source,
            $roleObj,
            $creator,
            $relevance
        );

        return $participation;
    }

    public function remove(Source $source, string $roleCode, $creatorID): bool
    {
        return (bool) $this->db->removeParticipation($source, $roleCode, $creatorID);
    }
}
