<?php

declare(strict_types=1);

namespace Arete\Logos\Domain\Contracts;

use Arete\Logos\Domain\Abstracts\CreatorType;
use Arete\Logos\Domain\Role;
use Arete\Logos\Domain\Source;

interface Participation
{
    public function creatorId(): int;
    public function creatorType(): CreatorType;
    public function role(): Role;
    public function relevance(): int;
    public function source(): Source;
}
