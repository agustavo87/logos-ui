<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Arete\Logos\Models\Schema;

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
        $this->seedCreatorTypes();

        $schemaLoader = app(\Arete\Logos\Services\Zotero\SchemaLoaderInterface::class);
        $schema = $schemaLoader->load();

        foreach ($schema->itemTypes as $itemType) {
            $sourceTypeCode = $itemType->itemType;
            foreach ($itemType->creatorTypes as $creatorType) {
                $roleCode = $creatorType->creatorType;
                if (!DB::table('roles')->where('code_name', $roleCode)->exists()) {
                    DB::table('roles')->insert([
                        'code_name' => $roleCode,
                        'primary'   => $creatorType->primary
                    ]);
                }
                DB::table('participation_types')->insert([
                    'source_type_code_name' => $sourceTypeCode,
                    'role_code_name'        => $roleCode
                ]);
            }
        }
    }

    public function seedCreatorTypes()
    {
        $creatorTypes = config('logos.creatorTypes.data');
        $version = config('logos.creatorTypes.version');

        foreach ($creatorTypes as $codeName => $data) {
            DB::table('creator_types')->insert([
                'code_name' => $codeName,
                'label'     => $data['label']
            ]);
            $schemaId = DB::table('schemas')->insertGetId([
                'type_code_name'    => $codeName,
                'type'              => Schema::Types['creator'],
                'version'           => $version,
                'created_at'        => now(),
                'updated_at'        => now()
            ]);
            $order = 0;
            foreach ($data['fields'] as $field) {
                $fieldCodeName = $field[0];
                $baseFieldCodeName = null;
                $fieldLabel = $field[1];
                $type = config('logos.fieldValueTypes')[$baseFieldCodeName] ??
                            config('logos.fieldValueTypes')['default'];
                DB::table('attribute_types')->insert([
                    'code_name'                     => $fieldCodeName,
                    'base_attribute_type_code_name' => $baseFieldCodeName,
                    'value_type'                    => $type
                ]);
                DB::table('schema_attributes')->insert([
                    'schema_id'                 => $schemaId,
                    'attribute_type_code_name'  => $fieldCodeName,
                    'label'                     => $fieldLabel,
                    'order'                     => $order++
                ]);
            }
        }
    }
}
