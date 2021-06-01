<?php

declare(strict_types=1);

namespace Arete\Logos\Models;

class RoleCollection extends Attributes
{
    protected SourceType $type;

    public function __construct($roles, $type)
    {
        $this->type = $type;
        foreach ($roles as $role) {
            $this->attributes[$role->code_name] = new Role($role);
        }
    }

    public function type(): SourceType
    {
        return $this->type;
    }
}
