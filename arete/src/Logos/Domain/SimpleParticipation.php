<?php

declare(strict_types=1);

namespace Arete\Logos\Domain;

use Arete\Logos\Domain\Contracts\Participation;
use Arete\Logos\Domain\Abstracts\CreatorType;
use Arete\Logos\Domain\Source;

class SimpleParticipation implements Participation
{
    protected int $relevance;
    protected ?Creator $creator = null;
    protected Role $role;
    protected Source $source;
    protected array $dirtyAttributes = [];

    public function __construct(
        Source $source,
        Role $role,
        Creator $creator,
        int $relevance
    ) {
        $this->source = $source;
        $this->role = $role;
        $this->creator = $creator;
        $this->role = $role;
        $this->relevance = $relevance;
    }

    public function __get($name)
    {
        return $this->creator->$name;
    }

    public function __set($name, $value)
    {
        return $this->creator->$name = $value;
    }

    public function count(): int
    {
        return $this->creator->count();
    }

     /**
     * Returns true if the class has the attribute
     *
     * @param string $attribute
     *
     * @return bool
     */
    public function has(string $attribute): bool
    {
        return $this->creator->has($attribute);
    }

    /**
     * Returns the attributes names
     *
     * @return string[]
     */
    public function attributes(): array
    {
        return $this->creator->attributes();
    }

    /**
     * Returns the attributes as associative attribute => value array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
        'role' => $this->role->code,
        'relevance' => $this->relevance,
        'creator' => $this->creator->toArray()
        ];
    }

    /**
     * Introduces a new attribute
     *
     * @param string $attribute
     * @param mixed $value
     *
     * @return void
     */
    public function pushAttribute(string $attribute, $value)
    {
        $this->creator->pushAttribute($attribute, $value);
    }

    public function creatorId(): int
    {
        return $this->creator->id();
    }

    public function creator(): Creator
    {
        return $this->creator;
    }

    public function creatorType(): CreatorType
    {
        return $this->creator->type();
    }

    public function role(): Role
    {
        return $this->role;
    }

    public function relevance(): int
    {
        return $this->relevance;
    }

    public function source(): Source
    {
        return $this->source;
    }

    public function setRelevance(int $relevance): Participation
    {
        if (!$this->isDirty('relevance')) {
            $this->dirtyAttributes['relevance'] = $this->relevance;
        }
        $this->relevance = $relevance;

        return $this;
    }

    /**
     * @param Role|string $role
     *
     * @return Participation
     * @todo update the participation set index
     */
    public function setRole($role): Participation
    {
        $roleCode = $role instanceof Role ? $role->code : $role;
        $this->validateRole($roleCode);
        if (!$this->isDirty('role')) {
            $this->dirtyAttributes['role'] = $this->role;
        }
        $previousRoleCode = $this->role->code;
        $this->role = $this->source->type()->roles()->$roleCode;
        $this->source->participations()->update(function (&$participations) use ($previousRoleCode) {
            unset($participations[$previousRoleCode][$this->creator->id()]);
            if (!count($participations[$previousRoleCode])) {
                unset($participations[$previousRoleCode]);
            }
        });
        $this->source->participations()->push($roleCode, $this);
        /* // For debug purposes
        $this->source->participations()->update(function ($allParticipations) {
            foreach ($allParticipations as $role => $roleParticipations) {
                echo "\n $role --------------\n";
                foreach ($roleParticipations as $creatorID => $participationData) {
                    echo "id:$creatorID |" . json_encode($participationData->toArray(), JSON_PRETTY_PRINT) . "\n";
                }
            }
        });
        //*/
        return $this;
    }

    /**
     * @param string $role
     * @throws \OutOfBoundsException
     * @return bool
     */
    protected function validateRole(string $role): bool
    {
        if ($this->source->type()->roles()->has($role)) {
            return true;
        }
        throw new \OutOfBoundsException(
            "The role '$role' do not belong to the source '{$this->source->type()->code()}'"
        );
    }

    public function isDirty(string $attribute = null): bool
    {
        if (!$attribute) {
            return count($this->dirtyAttributes) > 0;
        }
        return key_exists($attribute, $this->dirtyAttributes);
    }

    public function original(string $attribute)
    {
        return $this->dirtyAttributes[$attribute];
    }
}
