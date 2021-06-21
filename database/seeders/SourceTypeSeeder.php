<?php

namespace Database\Seeders;

use Arete\Logos\Models\Schema as LogosSchema;
use Arete\Logos\Models\Zotero\ZoteroSchema;
use Arete\Logos\Ports\Interfaces\ZoteroSchemaLoaderInterface;
use Arete\Logos\Infrastructure\Laravel\Common\DB as LogosDB;
use Arete\Logos\Interfaces\ValueTypeMapper;
use Arete\Logos\Interfaces\MapsSourceTypeLabels;
use Illuminate\Database\Seeder;

class SourceTypeSeeder extends Seeder
{
    protected LogosSchema $logosSchema;
    protected ZoteroSchema $zoteroSchema;
    protected LogosDB $db;
    protected ValueTypeMapper $valueTypes;
    protected MapsSourceTypeLabels $sourceTypeLabels;

    public function __construct(
        LogosSchema $logosSchema,
        ZoteroSchemaLoaderInterface $zoteroSchemaLoader,
        LogosDB $db,
        ValueTypeMapper $valueTypes,
        MapsSourceTypeLabels $sourceTypeLabels
    ) {
        $this->logosSchema = $logosSchema;
        $this->zoteroSchema = $zoteroSchemaLoader->load();
        $this->db = $db;
        $this->valueTypes = $valueTypes;
        $this->sourceTypeLabels = $sourceTypeLabels;
    }

    public function run()
    {
        $itemTypes = $this->zoteroSchema->itemTypes;

        foreach ($itemTypes as $itemType) {
            $sourceTypeCode = $itemType->itemType;

            $this->db->insertSourceType(
                $sourceTypeCode,
                $this->sourceTypeLabels->mapSourceTypeLabel($sourceTypeCode)
            );

            $schemaID = $this->db->insertSchema(
                $sourceTypeCode,
                $this->logosSchema::TYPES['source'],
                'z.' . $this->zoteroSchema->version
            );

            $order = 0;
            foreach ($itemType->fields as $field) {
                $baseAttribute = $field->baseField;
                $attribute = $field->field;

                if (!$this->db->attributeTypeExist($attribute)) {
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
