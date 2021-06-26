<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Laravel;

use Arete\Logos\Application\Ports\Interfaces\CreatorsRepository;
use Arete\Logos\Application\Ports\Interfaces\LogosEnviroment;
use Arete\Logos\Infrastructure\Laravel\Common\DBRepository;
use Arete\Logos\Application\Ports\Interfaces\ParticipationRepository;
use Arete\Logos\Domain\Contracts\Participation;
use Arete\Logos\Domain\Creator;
use Arete\Logos\Domain\SimpleParticipation;
use Arete\Logos\Domain\Source;
use Arete\Logos\Infrastructure\Laravel\Common\DB;

class DBParticipationRepository extends DBRepository implements ParticipationRepository
{
    protected CreatorsRepository $creators;

    public function __construct(
        DB $db,
        LogosEnviroment $logos,
        CreatorsRepository $creators
    ) {
        parent::__construct($db, $logos);
        $this->creators = $creators;
    }

    public function create(Source $source, Creator $creator, string $role, int $relevance): Participation
    {
        $roleObj = $source->type()->roles()->$role;

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

    public function load(Source $source): array
    {
        $participationsData = $this->db->getParticipations($source);
        $participations = [];
        foreach ($participationsData as $participationData) {
            $participations[] = new SimpleParticipation(
                $source,
                $source->type()->roles()->{$participationData->role_code_name},
                $this->creators->get($participationData->creator_id),
                $participationData->relevance
            );
        }
        return $participations;
    }

    /**
     * @param Participation[]|Participation $participations
     *
     * @return void
     */
    public function save($participations): int
    {
        return $this->db->saveParticipations($participations);
    }
}
