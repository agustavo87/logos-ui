<?php

declare(strict_types=1);

namespace Arete\Logos\Models;

use Illuminate\Support\Collection;

/**
 * Laravel dependent RoleCollection.
 */
class LvRoleCollection extends RoleCollection
{
    /**
     * Constructs from Laravel Data
     *
     * @param Collection $roles
     * @param SourceType $type
     *
     * @return RoleCollection
     */
    public static function fromLvData(
        Collection $roles,
        SourceType $type
    ): RoleCollection {
        $new = new static();
        $new->type = $type;
        foreach ($roles as $role) {
            $new->attributes[$role->code_name] = new Role(get_object_vars($role)) ;
        }
        return $new;
    }
}
