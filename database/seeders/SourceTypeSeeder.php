<?php

namespace Database\Seeders;

use Arete\Logos\Models\Schema;
use Arete\Logos\Services\Laravel\DB as LgDB;
use Illuminate\Database\Seeder;

class SourceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $schemaLoader = app(\Arete\Logos\Services\Zotero\SchemaLoaderInterface::class);
        $schema = $schemaLoader->load();
        $LgDB = new LgDB();

        $valueTypeMapper = app(\Arete\Logos\Services\ZoteroValueTypeMapper::class);
        $sourceTypeLabelMapper = app(\Arete\Logos\Services\MapsSourceTypeLabels::class);

        $itemTypes = $schema->itemTypes;
        foreach ($itemTypes as $itemType) {
            $sourceTypeCodeName = $itemType->itemType;
            $LgDB->insertSourceType(
                $sourceTypeCodeName,
                $sourceTypeLabelMapper->mapSourceTypeLabel($sourceTypeCodeName)
            );

            $schemaVersion = 'z.1.0';
            $schemaID = $LgDB->insertSchema(
                $sourceTypeCodeName,
                Schema::Types['source'],
                $schemaVersion,
            );

            $order = 0;
            foreach ($itemType->fields as $field) {
                $baseAttribute = $field->baseField;
                $attribute = $field->field;
                if (!$LgDB->attributeExist($attribute)) {
                    $logosType = $valueTypeMapper->mapValueType($attribute);
                    $LgDB->insertAttributeType(
                        $attribute,
                        $logosType,
                        $baseAttribute
                    );
                }
                $LgDB->insertSchemaAttribute(
                    $attribute,
                    $schemaID,
                    $order++
                );
            }
        }
    }
}
