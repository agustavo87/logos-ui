<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Arete\Logos\Models\Schema;
use Arete\Logos\Models\Zotero\Schema as ZoteroSchema;
use Arete\Logos\Services\Laravel\DB as LogosDB;
use Arete\Logos\Services\Zotero\SchemaLoaderInterface as ZoteroSchemaLoader;

class CreatorTypeSeeder extends Seeder
{

    protected LogosDB $db;
    protected ZoteroSchema $schema;

    public function __construct(
        LogosDB $db,
        ZoteroSchemaLoader $schemaLoader
    ) {
        $this->db = $db;
        $this->schema = $schemaLoader->load();
    }

    public function run()
    {

        $this->seedCreatorTypes();

        foreach ($this->schema->itemTypes as $itemType) {
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
        $creatorTypes = config('logos.creatorTypes.data');
        $version = config('logos.creatorTypes.version');

        foreach ($creatorTypes as $codeName => $data) {
            $this->db->insertCreatorType(
                $codeName,
                $data['label']
            );

            $schemaId = $this->db->insertSchema(
                $codeName,
                Schema::Types['creator'],
                $version
            );

            $order = 0;
            foreach ($data['fields'] as $field) {
                $fieldCodeName = $field[0];
                $baseFieldCodeName = null;
                $fieldLabel = $field[1];
                $type = config('logos.fieldValueTypes')[$baseFieldCodeName] ??
                            config('logos.fieldValueTypes')['default'];

                $this->db->insertAttributeType(
                    $fieldCodeName,
                    $type,
                    $baseFieldCodeName
                );

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
