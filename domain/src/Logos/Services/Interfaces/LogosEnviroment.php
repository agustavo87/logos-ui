<?php

declare(strict_types=1);

namespace Arete\Logos\Services\Interfaces;

interface LogosEnviroment
{
    public function getUsersTableData(): \stdClass;
}
