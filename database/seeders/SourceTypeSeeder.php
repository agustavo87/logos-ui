<?php

namespace Database\Seeders;

use Arete\Logos\Models\Schema;
use Arete\Logos\Services\Laravel\DB as LogosDB;
use Arete\Logos\Services\Zotero\SchemaLoaderInterface as ZoteroSchemaLoader;
use Arete\Logos\Models\Zotero\Schema as ZoteroSchema;
use Arete\Logos\Services\ZoteroValueTypeMapper;
use Arete\Logos\Services\MapsSourceTypeLabels;
use Illuminate\Database\Seeder;

class SourceTypeSeeder extends Seeder
{
    protected LogosDB $db;
    protected ZoteroSchema $schema;
    protected ZoteroValueTypeMapper $valueTypes;
    protected MapsSourceTypeLabels $sourceTypeLabels;

    public function __construct(
        LogosDB $db,
        ZoteroSchemaLoader $schemaLoader,
        ZoteroValueTypeMapper $valueTypes,
        MapsSourceTypeLabels $sourceTypeLabels
    ) {
        $this->db = $db;
        $this->schema = $schemaLoader->load();
        $this->valueTypes = $valueTypes;
        $this->sourceTypeLabels = $sourceTypeLabels;
    }

    public function run()
    {
        $itemTypes = $this->schema->itemTypes;

        foreach ($itemTypes as $itemType) {
            $sourceTypeCode = $itemType->itemType;

            $this->db->insertSourceType(
                $sourceTypeCode,
                $this->sourceTypeLabels->mapSourceTypeLabel($sourceTypeCode)
            );

            $schemaID = $this->db->insertSchema(
                $sourceTypeCode,
                Schema::Types['source'],
                'z.' . $this->schema->version
            );

            $order = 0;
            foreach ($itemType->fields as $field) {
                $baseAttribute = $field->baseField;
                $attribute = $field->field;

                if (!$this->db->attributeExist($attribute)) {
                    $this->db->insertAttributeType(
                        $attribute,
                        $this->valueTypes->mapValueType($attribute),
                        $baseAttribute
                    );
                }

                $this->db->insertSchemaAttribute(
                    $attribute,
                    $schemaID,
                    $order++
                );
            }
        }
    }
}
