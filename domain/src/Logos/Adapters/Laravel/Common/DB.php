<?php

declare(strict_types=1);

namespace Arete\Logos\Adapters\Laravel\Common;

use Illuminate\Support\Facades\DB as LvDB;
use Arete\Logos\Models\Schema;
use Arete\Logos\Ports\Interfaces\LogosEnviroment;

/**
 * Laravel depedent DB Access operations
 */
class DB
{
    protected LogosEnviroment $logos;
    public Schema $schema;

    public function __construct(
        LogosEnviroment $logos,
        Schema $schema
    ) {
        $this->logos = $logos;
        $this->schema = $schema;
    }

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

    public function getRoles($codeName)
    {
        return LvDB::table('participation_types')
            ->join('roles', 'participation_types.role_code_name', '=', 'roles.code_name')
            ->select('roles.*')
            ->where('participation_types.source_type_code_name', $codeName)
            ->get();
    }

    public function getSchema($codeName, $type)
    {
        return LvDB::table('schemas')
            ->where('type_code_name', $codeName)
            ->where('type', $type)
            ->latest()
            ->first();
    }

    public function getSourceSchema($codename)
    {
        return $this->getSchema($codename, $this->schema::TYPES['source']);
    }

    public function getCreatorSchema($codename)
    {
        return $this->getSchema($codename, $this->schema::TYPES['creator']);
    }

    public function getSourceType($codeName)
    {
        return LvDB::table('source_types')->where([
            'code_name' => $codeName,
        ])->first();
    }

    public function getCreatorType($codeName)
    {
        return LvDB::table('creator_types')->where([
            'code_name' => $codeName,
        ])->first();
    }

    public function getAttributes($schemaId)
    {
        return LvDB::table('schema_attributes')
            ->join(
                'attribute_types',
                'schema_attributes.attribute_type_code_name',
                '=',
                'attribute_types.code_name'
            )
            ->select(
                'attribute_types.code_name',
                'attribute_types.base_attribute_type_code_name',
                'attribute_types.value_type',
                'schema_attributes.label',
                'schema_attributes.order',
            )
            ->where('schema_attributes.schema_id', $schemaId)
            ->get();
    }

    public function insertSource($type, $userId, $updated = null, $created = null): int
    {
        $updated = $updated ?? now();
        $created = $created ?? now();
        return LvDB::table('sources')->insertGetId([
            'updated_at' => $updated,
            'created_at' => $created,
            $this->logos->getUsersTableData()->FK => $userId,
            'source_type_code_name' => $type
        ]);
    }

    public function getAttributeType($code): \stdClass
    {
        return LvDB::table('attribute_types')->where('code_name', $code)->first();
    }

    public function insertValue($type, $value): int
    {
        $valueTables = [
            'text' => 'text_values',
            'number' => 'number_values',
            'date' => 'date_values',
            'complex' => 'complex_values'
        ];
        $valTable = $valueTables[$type];
        return LvDB::table($valTable)->insertGetId([
            'data' => $value
        ]);
    }

    public function insertAttribute(
        $attributableId,
        $attributableType,
        $attributeType,
        $value
    ): ?int {
        $valueType = $this->getAttributeType($attributeType)->value_type;
        $valueID = $this->insertValue($valueType, $value);
        $attributableType = $this->schema::TYPES[$attributableType];
        $id = LvDB::table('attributes')->insertGetId([
            'attributable_id' => $attributableId,
            'attributable_type' => $attributableType,
            'attribute_type_code_name' => $attributeType,
            'value_type' => $valueType,
            'value_id' => $valueID
        ]);

        return $id;
    }
}