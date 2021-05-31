<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreatorTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    protected array $fieldsProperties;

    public function run()
    {

        $schemaLoader = app(\Arete\Logos\Services\Zotero\SchemaLoaderInterface::class);
        $schema = $schemaLoader->load();

        foreach ($schema->itemTypes as $itemType) {
            $sourceTypeCode = $itemType->itemType;
            foreach ($itemType->creatorTypes as $creatorType) {
                $roleCode = $creatorType->creatorType;
                if (!DB::table('roles')->where('code_name', $roleCode)->exists()) {
                    DB::table('roles')->insert([
                        'code_name'     => $roleCode,
                        'primary'       => $creatorType->primary,
                        'created_at'    => now(),
                        'updated_at'    => now()
                    ]);
                }
                DB::table('participation_types')->insert([
                    'source_type_code_name' => $sourceTypeCode,
                    'role_code_name'        => $roleCode
                ]);
            }
            // $this->createSchema()
        }
    }
}
