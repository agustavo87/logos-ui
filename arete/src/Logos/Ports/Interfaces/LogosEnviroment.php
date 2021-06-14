<?php

declare(strict_types=1);

namespace Arete\Logos\Ports\Interfaces;

interface LogosEnviroment
{
    public function getUsersTableData(): \stdClass;
}
