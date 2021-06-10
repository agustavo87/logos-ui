<?php

namespace Database\Seeders;

use Arete\Logos\Models\Schema;
use Arete\Logos\Services\Laravel\DB as LogosDB;
use Illuminate\Database\Seeder;

class SourceTypeSeeder extends Seeder
{
    protected LogosDB $db;

    public function __construct(
        LogosDB $db,
    ) {
        $this->db = $db;
    }

    public function run()
    {
        //
    }
}
