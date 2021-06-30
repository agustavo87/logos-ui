<?php

declare(strict_types=1);

namespace Arete\Logos\Domain;

use Arete\Common\Interfaces\Arrayable;
use Arete\Exceptions\IncorrectDataStructureException;
use Arete\Exceptions\PersistenceException;
use Arete\Logos\Domain\Traits\ExposeAttributes;
use Arete\Logos\Domain\Contracts\Participation;
use Arete\Logos\Application\Ports\Interfaces\CreatorsRepository;
use Arete\Logos\Application\Ports\Interfaces\ParticipationRepository;
use Arete\Logos\Domain\Source;

class ParticipationSet implements Arrayable
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

    /**
     * Loads persisted participations
     *
     * @return self
     */
    public function load(): self
    {
        $participations = $this->participations->load($this->source);
        foreach ($participations as $participation) {
            $this->push($participation->role()->code, $participation);
        }
        return $this;
    }
    public function save(): self
    {
        $dirtyCreators = [];
        $toBeSavedParticipations = [];
        foreach ($this->attributes as $roleCode => $participations) {
            foreach ($participations as $creatorID => $participation) {
                $creator = $participation->creator();
                if ($creator->isDirty()) {
                    // if there is more than one instance of same creator modified is undertimend wich is saved.
                    $dirtyCreators[$creator->id()] = $creator;
                }
                /** @todo ¿por qué no se chequea si está sucia la participación a guardar ? */
                $toBeSavedParticipations[] = $participation;
            }
        }
        foreach ($dirtyCreators as $creator) {
            $this->creators->save($creator);
        }
        if (count($toBeSavedParticipations)) {
            $this->participations->save($toBeSavedParticipations);
        }
        return $this;
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

    public function remove(string $roleCode, $creatorID): bool
    {
        //
        if (!isset($this->$roleCode[$creatorID])) {
            throw new \OutOfBoundsException(
                "There is no participation with role: $roleCode and creator id: $creatorID ."
            );
        }

        $removed = $this->participations->remove(
            $this->source,
            $roleCode,
            $creatorID
        );

        if (!$removed) {
            throw new PersistenceException("Could not remove creator: $creatorID with role: $roleCode");
        }

        unset($this->attributes[$roleCode][$creatorID]);
        return true;
    }

    public function toArray(): array
    {
        $participations = [];
        foreach ($this->attributes as $role => $participationsArray) {
            $participations[$role] = [];
            foreach ($participationsArray as $id => $participation) {
                $participations[$role][$id] = $participation->toArray();
            }
        }
        return $participations;
    }
}
