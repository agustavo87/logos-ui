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

        $this->fieldsProperties = $schema->meta['fields'];
        $itemTypes = $schema->itemTypes;
        foreach ($itemTypes as $itemType) {
            $sourceTypeCodeName = $itemType->itemType;
            DB::table('source_types')->insert([
                'code_name' => $sourceTypeCodeName,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            DB::table('schemas')->insert([
                'type_code_name'     => $sourceTypeCodeName,
                'type'          => config('logos.schemaTypes.source'),
                'version'       => 'z.1.0',
                'created_at'    => now(),
                'updated_at'    => now()
            ]);

            foreach ($itemType->fields as $field) {
                $logosType = $valueTypeMapper->mapValueType($field->field);
                if (!DB::table('base_attributes')->where('code_name', $field->field)->exists()) {
                    DB::table('base_attributes')->insert([
                        'code_name'     => $field->field,
                        'value_type'    => $logosType,
                        'created_at'    => now(),
                        'updated_at'    => now()
                    ]);
                } else {
                    Log::notice(
                        "Base item attribute with code '{$field->field}' already exists.",
                        ['itemType' => $itemType]
                    );
                }
            }

            // $this->createSchema()
        }
    }
}
