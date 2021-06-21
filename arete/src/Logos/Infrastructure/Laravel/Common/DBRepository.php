<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Laravel\Common;

use Arete\Logos\Infrastructure\Laravel\Common\DB;

/**
 * Laravel dependent Data Base based Repository
 */
abstract class DBRepository
{
    protected DB $db;

    public function __construct(DB $db)
    {
        $this->db = $db;
    }
}
