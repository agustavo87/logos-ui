<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Arete\Logos\Models\Schema;
use Arete\Logos\Services\Laravel\DB as LgDB;

class CreatorTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    protected LgDB $LgDB;

    public function run()
    {
        $this->LgDB = new LgDB();

        $this->seedCreatorTypes();

        $schemaLoader = app(\Arete\Logos\Services\Zotero\SchemaLoaderInterface::class);
        $schema = $schemaLoader->load();

        foreach ($schema->itemTypes as $itemType) {
            $sourceTypeCode = $itemType->itemType;
            foreach ($itemType->creatorTypes as $creatorType) {
                $roleCode = $creatorType->creatorType;
                if (!$this->LgDB->roleExist($roleCode)) {
                    $this->LgDB->insertRole($roleCode, $creatorType->primary);
                }
                $this->LgDB->insertParticipationType($sourceTypeCode, $roleCode);
            }
        }
    }

    public function seedCreatorTypes()
    {
        $creatorTypes = config('logos.creatorTypes.data');
        $version = config('logos.creatorTypes.version');

        foreach ($creatorTypes as $codeName => $data) {
            $this->LgDB->insertCreatorType(
                $codeName,
                $data['label']
            );

            $schemaId = $this->LgDB->insertSchema(
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

                $this->LgDB->insertAttributeType(
                    $fieldCodeName,
                    $type,
                    $baseFieldCodeName
                );

                $this->LgDB->insertSchemaAttribute(
                    $fieldCodeName,
                    $schemaId,
                    $order++,
                    $fieldLabel
                );
            }
        }
    }
}
