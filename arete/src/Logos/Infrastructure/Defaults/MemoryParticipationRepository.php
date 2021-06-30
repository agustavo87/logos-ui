<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Defaults;

use Arete\Logos\Application\Ports\Interfaces\ParticipationRepository;
use Arete\Logos\Domain\Contracts\Participation;
use Arete\Logos\Domain\SimpleParticipation;
use Arete\Logos\Domain\Source;
use Arete\Logos\Domain\Creator;

class MemoryParticipationRepository implements ParticipationRepository
{
    protected array $participations = [];

    public function create(Source $source, Creator $creator, string $role, int $relevance): Participation
    {
        // create participation
        $participation = new SimpleParticipation(
            $source,
            $source->type()->roles()->$role,
            $creator,
            $relevance
        );

        // store it
        if (!array_key_exists($source->id(), $this->participations)) {
            $this->participations[$source->id()] = [];
        }
        if (!array_key_exists($role, $this->participations[$source->id()])) {
            $this->participations[$source->id()][$role] = [];
        }
        $this->participations[$source->id()][$role][$creator->id()] = $participation;

        // return it
        return $participation;
    }

    public function save($participations): int
    {
        if (is_array($participations)) {
            $count = 0;
            foreach ($participations as $participation) {
                $this->saveParticipation($participation);
                $count++;
            }
            return $count;
        }
        $this->saveParticipation($participations);
        return 1;
    }

    /**
     * @param Participation $participation
     *
     * @return bool
     */
    protected function saveParticipation(Participation $participation): bool
    {
        // this may be not neccessary becous the partipation object is already modified becouse
        // is passed by reference
        $this->participations[$participation->source()->id()]
                             [$participation->role()->code]
                             [$participation->creatorId()] = $participation;
        return true;
    }

    public function remove(Source $source, string $roleCode, $creatorID): bool
    {
        unset($this->participations[$source->id()][$roleCode][$creatorID]);
        return true;
    }

    public function load(Source $source): array
    {
        $collapsedParticipations = [];
        foreach ($this->participations[$source->id()] as $role => $participations) {
             $collapsedParticipations = array_values($participations);
        }
        return $collapsedParticipations;
    }
}
