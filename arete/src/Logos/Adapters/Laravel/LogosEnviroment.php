<?php

declare(strict_types=1);

namespace Arete\Logos\Adapters\Laravel;

use Arete\Logos\Ports\Interfaces\LogosEnviroment as LogosEnviromentPort;
use Arete\Logos\Ports\Abstracts\ConfigurationRepository;
use Illuminate\Support\Str;

class LogosEnviroment implements LogosEnviromentPort
{
    protected ConfigurationRepository $config;

    public function __construct(ConfigurationRepository $config)
    {
        $this->config = $config;
    }
    public function getUsersTableData(): \stdClass
    {
        $usersTable = $this->config->get('usersTable') ?? 'users';
        $SingularTableName = Str::singular($usersTable);
        $usersPK = $this->config->get('usersPK') ??  'id';
        $usersFK = "{$SingularTableName}_{$usersPK}";
        return (object) [
            'table' => $usersTable,
            'PK'   => $usersPK,
            'FK' => $usersFK
        ];
    }
}
