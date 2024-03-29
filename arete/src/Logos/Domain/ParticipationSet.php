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
use Illuminate\Support\Facades\Log;

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
                    // in each role a creator can be only once per source
                    // if there is more than one instance of same creator modified is undetermined wich is saved.
                    $dirtyCreators[$creator->id()] = $creator;
                }
                /** @todo ¿por qué no se chequea si está sucia la participación a guardar ? */
                if ($participation->isDirty()) {
                    $toBeSavedParticipations[] = $participation;
                }
            }
        }
        // save dirty creators
        foreach ($dirtyCreators as $creator) {
            $this->creators->save($creator);
        }
        // save participations
        if (count($toBeSavedParticipations)) {
            $this->participations->save($toBeSavedParticipations);
        }
        return $this;
    }

    public function update(callable $strategy): self
    {
        $strategy($this->attributes);
        return $this;
    }

    public function push(string $role, Participation $participation)
    {
        if (!array_key_exists($role, $this->attributes)) {
            $this->attributes[$role] = [];
        }
        $this->attributes[$role][$participation->creatorId()] = $participation;
    }

    public function getByCreatorID($id): ?Participation
    {
        foreach ($this->attributes as $role => $participations) {
            if (in_array($id, array_keys($participations))) {
                return $participations[$id];
            }
        }
        return null;
    }

    public function getCreatorsIDs(): array
    {
        return array_reduce(
            array_values($this->attributes),
            fn($creatorIDs, $participations) => array_merge($creatorIDs, array_keys($participations)),
            []
        );
    }

    public function pushNew(array $creatorData, string $role, int $relevance): Participation
    {
        if (array_key_exists('creatorID', $creatorData)) {
            // The creator exists, just getit
            $creator = $this->creators->get($creatorData['creatorID']);
        } elseif (isset($creatorData['id']) && $creatorData['id'] != null) {
            // The creator exists, but has to be updated
            $creator = $this->updateCreator($creatorData);
        } elseif (array_key_exists('attributes', $creatorData) && array_key_exists('type', $creatorData)) {
            // The creator don't exist, have to be created.
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

    // Warining, the type of the creator can't be changed.
    protected function updateCreator($data)
    {
        $creator = $this->creators->get($data['id']);
        foreach ($creator->attributes() as $attributeName) {
            $creator->$attributeName = $data['attributes'][$attributeName];
        }
        $this->creators->save($creator);
        // ¿será necesario obtener una instancia fresca?
        return $creator;
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
    public function byRelevance(?string $role = null): array
    {
        if ($role === null) {
            $participations = array_merge(...array_values($this->attributes));
        } else {
            $participations = $this->$role;
        }
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

    public function removeByCreatorId($id)
    {
        $participation = $this->getByCreatorID($id);
        $this->remove((string) $participation->role(), $participation->creatorId());
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

    /**
     * Returns an array representation of participations
     *
     * @param string|null   $orderBy  'id' | 'relevance' | other;
     *                              - 'id': key by participation id.
     *                              - 'relevance': order by participation relevance (no key).
     *                              - other: no key or order.
     * @return array
     */
    public function toArray(?string $orderBy = 'id'): array
    {
        $participations = [];
        foreach ($this->attributes as $role => $participationsArray) {
            $participations[$role] = [];
            if ($orderBy == 'relevance') {
                foreach ($this->byRelevance($role) as $participation) {
                    $participations[$role][] = $participation->toArray();
                }
            } else {
                foreach ($participationsArray as $id => $participation) {
                    if ($orderBy == 'id') {
                        $participations[$role][$id] = $participation->toArray();
                    } else {
                        $participations[$role][] = $participation->toArray();
                    }
                }
            }
        }
        return $participations;
    }
}
