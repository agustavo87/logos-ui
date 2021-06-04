<?php

namespace Database\Seeders;

use Arete\Logos\Models\Schema;
use Arete\Logos\Services\Laravel\DB as LgDB;
use Arete\Logos\Services\Zotero\SchemaLoaderInterface;
use Arete\Logos\Services\ZoteroValueTypeMapper;
use Arete\Logos\Services\MapsSourceTypeLabels;
use Illuminate\Database\Seeder;

class SourceTypeSeeder extends Seeder
{

    protected LgDB $db;
    protected SchemaLoaderInterface $schema;
    protected ZoteroValueTypeMapper $valueTypes;
    protected MapsSourceTypeLabels $sourceTypeLabels;

    public function __construct(
        LgDB $db,
        SchemaLoaderInterface $schema,
        ZoteroValueTypeMapper $valueTypes,
        MapsSourceTypeLabels $sourceTypeLabels
    ) {
        $this->db = $db;
        $this->schema = $schema;
        $this->valueTypes = $valueTypes;
        $this->sourceTypeLabels = $sourceTypeLabels;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $schema = $this->schema->load();
        $itemTypes = $schema->itemTypes;

        foreach ($itemTypes as $itemType) {

            $sourceTypeCode = $itemType->itemType;

            $this->db->insertSourceType(
                $sourceTypeCode,
                $this->sourceTypeLabels->mapSourceTypeLabel($sourceTypeCode)
            );

            $schemaID = $this->db->insertSchema(
                $sourceTypeCode,
                Schema::Types['source'],
                'z.' . $schema->version
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
