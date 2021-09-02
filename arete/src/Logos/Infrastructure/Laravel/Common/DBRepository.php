<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Laravel\Common;

use Arete\Logos\Infrastructure\Laravel\Common\DB;
use Arete\Logos\Application\Ports\Interfaces\LogosEnviroment;

/**
 * Laravel dependent Data Base based Repository
 */
abstract class DBRepository
{
    protected DB $db;
    protected LogosEnviroment $env;

    public function __construct(DB $db, LogosEnviroment $env)
    {
        $this->db = $db;
        $this->env = $env;
    }
}
