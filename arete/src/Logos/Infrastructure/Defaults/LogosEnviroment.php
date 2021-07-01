<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Defaults;

use Arete\Logos\Application\Ports\Interfaces\LogosEnviroment as LogosEnviromentPort;
use Arete\Logos\Application\Ports\Abstracts\ConfigurationRepository;
use Arete\Logos\Infrastructure\Defaults\Support\Inflect;

class LogosEnviroment implements LogosEnviromentPort
{
    protected ConfigurationRepository $config;
    protected $defaultOwner;

    public function __construct(ConfigurationRepository $config)
    {
        $this->config = $config;
        $this->defaultOwner = $config->get('defaultOwner');
    }
    public function getOwnersTableData(): \stdClass
    {
        $inflector = new Inflect();
        $ownersTable = $this->config->get('ownersTable') ?? 'users';
        $SingularTableName = $inflector->singularize($ownersTable);
        $ownersPK = $this->config->get('ownersPK') ??  'id';
        $ownersFK = "{$SingularTableName}_{$ownersPK}";
        return (object) [
            'table' => $ownersTable,
            'PK'   => $ownersPK,
            'FK' => $ownersFK
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
