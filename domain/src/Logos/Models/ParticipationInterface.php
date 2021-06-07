<?php

declare(strict_types=1);

namespace Arete\Logos\Models;

interface ParticipationInterface
{
    public function creatorId(): int;
    public function creatorType(): CreatorType;
    public function role(): Role;
    public function relevance(): int;
}
