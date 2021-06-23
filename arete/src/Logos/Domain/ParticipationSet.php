<?php

declare(strict_types=1);

namespace Arete\Logos\Domain;

use Arete\Exceptions\IncorrectDataStructureException;
use Arete\Logos\Domain\Traits\ExposeAttributes;
use Arete\Logos\Domain\Contracts\Participation;
use Arete\Logos\Application\Ports\Interfaces\CreatorsRepository;
use Arete\Logos\Application\Ports\Interfaces\ParticipationRepository;
use Arete\Logos\Domain\Source;

class ParticipationSet
{
    use ExposeAttributes;

    protected Source $source;
    protected CreatorsRepository $creators;
    protected ParticipationRepository $participations;

    public function __construct(
        Source $source,
        CreatorsRepository $creators,
        ParticipationRepository $participations
    ) {
        $this->source = $source;
        $this->creators = $creators;
        $this->participations = $participations;
    }

    public function push(string $role, Participation $participation)
    {
        if (!array_key_exists($role, $this->attributes)) {
            $this->attributes[$role] = [];
        }
        $this->attributes[$role][$participation->creatorId()] = $participation;
    }

    public function pushNew(array $creatorData, string $role, int $relevance): Participation
    {
        if (array_key_exists('creatorID', $creatorData)) {
            $creator = $this->creators->get($creatorData['creatorID']);
        } elseif (array_key_exists('attributes', $creatorData) && array_key_exists('type', $creatorData)) {
            $creator = $this->creators->createFromArray(
                [
                    'type' => $creatorData['type'],
                    'attributes' => $creatorData['attributes']
                ],
                $this->source()->ownerID()
            );
        } else {
            throw new IncorrectDataStructureException(
                'Either creatorID or attributes and type has to be provided to create a participation.'
            );
        }
        $participation = $this->participations->create($this->source, $creator, $role, $relevance);
        $this->push($role, $participation);
        return $participation;
    }

    /**
     * @return string[]
     */
    public function roles(): array
    {
        return $this->attributes();
    }

    public function source(): Source
    {
        return $this->source;
    }

    /**
     * @param string $role
     *
     * @return Participation[]
     */
    public function byRelevance(string $role): array
    {
        $participations = $this->$role;
        usort($participations, function ($a, $b) {
            $a = $a->relevance();
            $b = $b->relevance();
            if ($a == $b) {
                return 0;
            } elseif ($a > $b) {
                return 1;
            } else {
                return -1;
            }
        });
        return $participations;
    }
}
