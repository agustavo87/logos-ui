<?php

declare(strict_types=1);

namespace Arete\Logos\Domain\Contracts;

use Arete\Common\Interfaces\Arrayable;
use Arete\Logos\Domain\Abstracts\CreatorType;
use Arete\Logos\Domain\Creator;
use Arete\Logos\Domain\Role;
use Arete\Logos\Domain\Source;

interface Participation extends Arrayable
{
    public function source(): Source;
    public function creator(): Creator;
    public function creatorId(): int;
    public function creatorType(): CreatorType;
    public function role(): Role;
    public function relevance(): int;
    public function setRelevance(int $relevance): self;

    /**
     * Indicates if the own attributes of the participations are dirty
     *
     * @param string|null $attribute
     *
     * @return bool
     */
    public function isDirty(string $attribute = null): bool;

    /**
     * @param string $attribute
     *
     * @return void
     */
    public function original(string $attribute);

    /**
     * @param Role|string $role
     *
     * @return self
     */
    public function setRole($role): self;
}
