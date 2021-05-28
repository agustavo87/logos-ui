<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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

        $this->fieldsProperties = $schema->meta['fields'];
        $itemTypes = $schema->itemTypes;
        print_r($itemTypes);
        foreach ($itemTypes as $itemType) {
            $sourceTypeCodeName = $itemType->itemType;
            echo "type\n";
            print_r($sourceTypeCodeName);
            // DB::table('source_types')->insert([
            //     'code_name' => $sourceTypeCodeName
            // ]);
            // $this->createSchema()
        }
    }
}
