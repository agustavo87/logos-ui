<?php

declare(strict_types=1);

namespace Arete\Logos\Repositories\Laravel\Conceptual;

use Arete\Logos\Services\Laravel\DB;

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
