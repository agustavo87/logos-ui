<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Arete\Logos\Models\Schema;
use Arete\Logos\Models\Zotero\Schema as ZoteroSchema;
use Arete\Logos\Adapters\Laravel\Common\DB as LogosDB;
use Arete\Logos\Models\Schema as LogosSchema;
use Arete\Logos\Services\Zotero\SchemaLoaderInterface as ZoteroSchemaLoader;
use Arete\Logos\Services\ZoteroValueTypeMapper;

class CreatorTypeSeeder extends Seeder
{
    protected LogosDB $db;
    protected ZoteroSchema $zoteroSchema;
    protected ZoteroValueTypeMapper $valueTypes;
    protected LogosSchema $logosSchema;

    public function __construct(
        LogosDB $db,
        ZoteroSchemaLoader $zoteroSchemaLoader,
        ZoteroValueTypeMapper $valueTypes,
        LogosSchema $logosSchema
    ) {
        $this->db = $db;
        $this->zoteroSchema = $zoteroSchemaLoader->load();
        $this->valueTypes = $valueTypes;
        $this->logosSchema = $logosSchema;
    }

    public function run()
    {

        $this->seedCreatorTypes();

        foreach ($this->zoteroSchema->itemTypes as $itemType) {
            $sourceTypeCode = $itemType->itemType;
            foreach ($itemType->creatorTypes as $creatorType) {
                $roleCode = $creatorType->creatorType;
                if (!$this->db->roleExist($roleCode)) {
                    $this->db->insertRole($roleCode, $creatorType->primary);
                }
                $this->db->insertParticipationType($sourceTypeCode, $roleCode);
            }
        }
    }

    public function seedCreatorTypes()
    {
        foreach ($this->logosSchema->creatorTypes() as $codeName => $data) {
            $this->db->insertCreatorType(
                $codeName,
                $data['label']
            );

            $schemaId = $this->db->insertSchema(
                $codeName,
                Schema::TYPES['creator'],
                Schema::VERSION
            );

            $order = 0;

            foreach ($data['fields'] as $field) {
                $fieldCodeName = $field[0];
                $fieldLabel = $field[1];

                if (!$this->db->attributeExist($fieldCodeName)) {
                    $this->db->insertAttributeType(
                        $fieldCodeName,
                        $this->valueTypes->mapValueType($fieldCodeName)
                    );
                }

                $this->db->insertSchemaAttribute(
                    $fieldCodeName,
                    $schemaId,
                    $order++,
                    $fieldLabel
                );
            }
        }
    }
}
