<?php

declare(strict_types=1);

namespace Arete\Logos\Models;

abstract class SourceType extends AbstractType
{
    protected RoleCollection $roles;

    public function participations(): RoleCollection
    {
        return $this->roles;
    }
}