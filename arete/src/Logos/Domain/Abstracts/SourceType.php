<?php

declare(strict_types=1);

namespace Arete\Logos\Domain\Abstracts;

abstract class SourceType extends Type
{
    protected ?string $genus = 'source';
    protected RoleCollection $roles;

    public function participations(): RoleCollection
    {
        return $this->roles;
    }
}
