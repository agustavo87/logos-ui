<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Laravel;

use Arete\Logos\Application\Ports\Interfaces\LogosEnviroment as LogosEnviromentPort;
use Arete\Logos\Application\Ports\Abstracts\ConfigurationRepository;
use Illuminate\Support\Str;

class LogosEnviroment implements LogosEnviromentPort
{
    protected ConfigurationRepository $config;
    protected $defaultOwner;

    public function __construct(ConfigurationRepository $config)
    {
        $this->config = $config;
        $this->defaultOwner = $config->get('defaultOwner');
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

    public function setOwner(string $id)
    {
        $this->defaultOwner = $id;
    }

    public function getOwner()
    {
        return $this->defaultOwner;
    }
}
