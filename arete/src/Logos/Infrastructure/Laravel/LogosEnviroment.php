<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Laravel;

use Arete\Logos\Application\Ports\Interfaces\LogosEnviroment as LogosEnviromentPort;
use Arete\Logos\Application\Ports\Abstracts\ConfigurationRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LogosEnviroment implements LogosEnviromentPort
{
    protected ConfigurationRepository $config;
    protected $defaultOwner;

    public function __construct(ConfigurationRepository $config)
    {
        $this->config = $config;
        $this->defaultOwner = $this->config->get('defaultOwner');
    }
    public function getOwnersTableData(): \stdClass
    {
        $ownersTable = $this->config->get('ownersTable') ?? 'users';
        $SingularTableName = Str::singular($ownersTable);
        $ownersPK = $this->config->get('ownersPK') ??  'id';
        $ownersFK = "{$SingularTableName}_{$ownersPK}";
        return (object) [
            'table' => $ownersTable,
            'PK'   => $ownersPK,
            'FK' => $ownersFK
        ];
    }

    public function authenticated(): bool
    {
        return Auth::check();
    }

    public function setOwner($id)
    {
        $this->defaultOwner = $id;
    }

    public function getOwner()
    {
        return $this->authenticated() ?
            Auth::user()->id :
            $this->defaultOwner;
    }
}
