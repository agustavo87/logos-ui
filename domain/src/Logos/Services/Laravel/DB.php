<?php

declare(strict_types=1);

namespace Arete\Logos\Services\Laravel;

use Illuminate\Support\Facades\DB as LvDB;

class DB
{
    public function insertSourceType($code, $label = null)
    {
        LvDB::table('source_types')->insert([
            'code_name' => $code,
            'label'     => $label
        ]);
    }

    public function insertSchema(
        $code,
        $type,
        $version,
        $created = null,
        $updated = null
    ): int {
        $created = $created ?? now();
        $updated = $updated ?? now();
        return LvDB::table('schemas')->insertGetId([
            'type_code_name'    => $code,
            'type'              => $type,
            'version'           => $version,
            'created_at'        => $created,
            'updated_at'        => $updated
        ]);
    }

    public function attributeExist($code): bool
    {
        return LvDB::table('attribute_types')->where('code_name', $code)->exists();
    }

    public function insertAttributeType($code, $valueType, $base_code = null)
    {
        LvDB::table('attribute_types')->insert([
            'code_name'                     => $code,
            'value_type'                    => $valueType,
            'base_attribute_type_code_name' => $base_code,
        ]);
    }

    public function insertSchemaAttribute(
        $attributeTypeCode,
        $schemaID,
        int $order,
        $label = null
    ) {
        LvDB::table('schema_attributes')->insert([
            'attribute_type_code_name'  => $attributeTypeCode,
            'schema_id'                 => $schemaID,
            'order'                     => $order,
            'label'                     => $label
        ]);
    }

    public function insertCreatorType($code, $label = null)
    {
        LvDB::table('creator_types')->insert([
            'code_name' => $code,
            'label'     => $label
        ]);
    }

    public function roleExist($code): bool
    {
        return LvDB::table('roles')->where('code_name', $code)->exists();
    }

    public function insertRole($code, bool $primary = false)
    {
        LvDB::table('roles')->insert([
            'code_name' => $code,
            'primary'   => $primary
        ]);
    }

    public function insertParticipationType($sourceTypeCode, $roleCode)
    {
        LvDB::table('participation_types')->insert([
            'source_type_code_name' => $sourceTypeCode,
            'role_code_name'        => $roleCode
        ]);
    }
}
