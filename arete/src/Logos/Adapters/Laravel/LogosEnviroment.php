<?php

declare(strict_types=1);

namespace Arete\Logos\Adapters\Laravel;

use Arete\Logos\Ports\Interfaces\LogosEnviroment as LogosEnviromentPort;
use Illuminate\Support\Str;

class LogosEnviroment implements LogosEnviromentPort
{
    public function getUsersTableData(): \stdClass
    {
        $usersTable = config('sources.usersTable', 'users');
        $SingularTableName = Str::singular($usersTable);
        $usersPK = config('sources.usersPK', 'id');
        $usersFK = "{$SingularTableName}_{$usersPK}";
        return (object) [
            'table' => $usersTable,
            'PK'   => $usersPK,
            'FK' => $usersFK
        ];
    }
}
