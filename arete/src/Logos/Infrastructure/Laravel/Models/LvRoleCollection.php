<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Laravel\Models;

use Illuminate\Support\Collection;
use Arete\Logos\Domain\Role;
use Arete\Logos\Domain\SourceType;
use Arete\Logos\Domain\RoleCollection;

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
            $new->attributes[$role->code_name] = new Role([
                'code' => $role->code_name,
                'label' => $role->label,
                'primary' => (bool) $role->primary
            ]);
        }
        return $new;
    }
}
