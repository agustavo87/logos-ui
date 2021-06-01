<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SourceTypeSeeder extends Seeder
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

        $valueTypeMapper = app(\Arete\Logos\Services\ZoteroValueTypeMapper::class);

        $itemTypes = $schema->itemTypes;
        foreach ($itemTypes as $itemType) {
            $sourceTypeCodeName = $itemType->itemType;
            DB::table('source_types')->insert([
                'code_name' => $sourceTypeCodeName,
                'label'     => config("logos.source.types.{$sourceTypeCodeName}.label"),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $schemaVersion = 'z.1.0';
            $schemaID = DB::table('schemas')->insertGetId([
                'type_code_name'    => $sourceTypeCodeName,
                'type'              => config('logos.schemaTypes.source'),
                'version'           => $schemaVersion,
                'created_at'        => now(),
                'updated_at'        => now()
            ]);

            $order = 0;
            foreach ($itemType->fields as $field) {
                $baseAttritbute = $field->baseField ?? $field->field;
                $attribute = $field->field;
                if (!DB::table('base_attributes')->where('code_name', $baseAttritbute)->exists()) {
                    $logosType = $valueTypeMapper->mapValueType($baseAttritbute);
                    DB::table('base_attributes')->insert([
                        'code_name'     => $baseAttritbute,
                        'value_type'    => $logosType,
                        'created_at'    => now(),
                        'updated_at'    => now()
                    ]);
                }
                DB::table('schema_attributes')->insert([
                    'base_attribute_code_name' => $baseAttritbute,
                    'schema_id' => $schemaID,
                    'code_name' => "{$attribute}:{$sourceTypeCodeName}:{$schemaVersion}",
                    'front_attribute_code_name' => $attribute,
                    'order' => $order++,
                    'created_at'    => now(),
                    'updated_at'    => now()
                ]);
            }

            // $this->createSchema()
        }
    }
}
