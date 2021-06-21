<?php

declare(strict_types=1);

namespace Arete\Logos\Domain;

use Arete\Logos\Domain\Traits\ExposeAttributes;
use Arete\Logos\Domain\Contracts\Participation;
use Arete\Logos\Domain\Abstracts\CreatorType;

class SimpleParticipation implements Participation
{
    use ExposeAttributes;

    protected int $creatorId;
    protected int $relevance;
    protected CreatorType $creatorType;
    protected Role $role;

    public function __construct(array $params)
    {
        $this->attributes = $params['attributes'];
        $this->creatorId = $params['creatorId'];
        $this->relevance = $params['relevance'];
        $this->creatorType = $params['creatorType'];
        $this->role = $params['role'];
    }

    public function creatorId(): int
    {
        return $this->creatorId;
    }

    public function creatorType(): CreatorType
    {
        return $this->creatorType;
    }

    public function role(): Role
    {
        return $this->role;
    }

    public function relevance(): int
    {
        return $this->relevance;
    }
}
