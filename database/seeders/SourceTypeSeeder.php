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
                'label'     => config("logos.source.types.{$sourceTypeCodeName}.label")
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
                $baseAttribute = $field->baseField;
                $attribute = $field->field;
                if (!DB::table('attribute_types')->where('code_name', $attribute)->exists()) {
                    $logosType = $valueTypeMapper->mapValueType($attribute);
                    DB::table('attribute_types')->insert([
                        'code_name'                     => $attribute,
                        'base_attribute_type_code_name' => $baseAttribute,
                        'value_type'                    => $logosType,
                    ]);
                }
                DB::table('schema_attributes')->insert([
                    'attribute_type_code_name'  => $attribute,
                    'schema_id'                 => $schemaID,
                    'order'                     => $order++,
                ]);
            }
        }
    }
}
