<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Arete\Logos\Domain\Schema;
use Arete\Logos\Domain\Zotero\ZoteroSchema;
use Arete\Logos\Application\Ports\Interfaces\ZoteroSchemaLoaderInterface;
use Arete\Logos\Infrastructure\Laravel\Common\DB as LogosDB;
use Arete\Logos\Application\Interfaces\ValueTypeMapper;

class CreatorTypeSeeder extends Seeder
{
    protected LogosDB $db;
    protected ZoteroSchema $zoteroSchema;
    protected ValueTypeMapper $valueTypes;
    protected Schema $logosSchema;

    public function __construct(
        LogosDB $db,
        ZoteroSchemaLoaderInterface $zoteroSchemaLoader,
        ValueTypeMapper $valueTypes,
        Schema $logosSchema
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
                $this->logosSchema::TYPES['creator'],
                $this->logosSchema::VERSION
            );

            $order = 0;

            foreach ($data['fields'] as $field) {
                $fieldCodeName = $field[0];
                $fieldLabel = $field[1];

                if (!$this->db->attributeTypeExist($fieldCodeName)) {
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
