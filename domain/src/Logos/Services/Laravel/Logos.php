<?php

declare(strict_types=1);

namespace Arete\Logos\Services\Laravel;

use Arete\Logos\Services\Interfaces\LogosEnviroment;
use Illuminate\Support\Str;

class Logos implements LogosEnviroment
{
    public function getUsersTableData(): \stdClass
    {
        $usersTable = config('usersTable', 'users');
        $SingularTableName = Str::singular($usersTable);
        $usersPK = config('usersPK', 'id');
        $usersFK = "{$SingularTableName}_{$usersPK}";
        return (object) [
            'table' => $usersTable,
            'PK'   => $usersPK,
            'FK' => $usersFK
        ];
    }
}
