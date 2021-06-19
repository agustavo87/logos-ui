<?php

declare(strict_types=1);

namespace Arete\Logos\Models;

use Arete\Logos\Models\Abstracts\Type;

abstract class SourceType extends Type
{
    protected ?string $genus = 'source';
    protected RoleCollection $roles;

    public function participations(): RoleCollection
    {
        return $this->roles;
    }
}
