<?php

namespace Database\Seeders;

use Arete\Logos\Models\Schema;
use Arete\Logos\Models\Zotero\ZoteroSchema;
use Arete\Logos\Adapters\Laravel\Common\DB as LogosDB;
use Arete\Logos\Interfaces\ValueTypeMapper;
use Arete\Logos\Interfaces\MapsSourceTypeLabels;
use Arete\Logos\Ports\Logos;
use Illuminate\Database\Seeder;

class SourceTypeSeeder extends Seeder
{
    protected LogosDB $db;
    protected ZoteroSchema $schema;
    protected ValueTypeMapper $valueTypes;
    protected MapsSourceTypeLabels $sourceTypeLabels;

    public function __construct(
        LogosDB $db
    ) {
        $this->db = $db;
        $this->schema = Logos::zoteroSchema('simple');
        $this->valueTypes = Logos::valueTypes();
        $this->sourceTypeLabels = Logos::sourceTypeLabels();
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
                Schema::TYPES['source'],
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
