<?php

declare(strict_types=1);

namespace Arete\Logos\Application\Ports\Interfaces;

interface LogosEnviroment
{
    public function getUsersTableData(): \stdClass;
}
