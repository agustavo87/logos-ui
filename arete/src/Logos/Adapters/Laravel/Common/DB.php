<?php

declare(strict_types=1);

namespace Arete\Logos\Adapters\Laravel\Common;

use Arete\Logos\Models\Abstracts\Attributable;
use Illuminate\Support\Facades\DB as LvDB;
use Arete\Logos\Models\Schema;
use Arete\Logos\Ports\Interfaces\LogosEnviroment;
use Illuminate\Support\Collection;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;

/**
 * Laravel depedent DB Access operations
 */
class DB
{
    public const VALUE_COLUMS = [
        'text' => 'text_value',
        'number' => 'number_value',
        'date' => 'date_value',
        'complex' => 'complex_value'
    ];

    protected LogosEnviroment $logos;
    public Connection $db;
    public Schema $schema;

    public function __construct(
        LogosEnviroment $logos,
        Schema $schema,
        DatabaseManager $dbManager
    ) {
        $this->logos = $logos;
        $this->schema = $schema;
        $this->db = $dbManager->connection();
    }

    public function insertSourceType($code, $label = null): bool
    {
        return LvDB::table('source_types')->insert([
            'code_name' => $code,
            'label'     => $label
        ]);
    }

    /**
     * @param mixed $code
     * @param mixed $type
     * @param mixed $version
     * @param null $created
     * @param null $updated
     *
     * @return int id of the new schema
     */
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

    public function insertAttributeType($code, $valueType, $base_code = null): bool
    {
        return LvDB::table('attribute_types')->insert([
            'code_name'                     => $code,
            'value_type'                    => $valueType,
            'base_attribute_type_code_name' => $base_code,
        ]);
    }

    public function attributeTypeExist($code): bool
    {
        return LvDB::table('attribute_types')->where('code_name', $code)->exists();
    }

    /**
     * @param mixed $code
     *
     * @return \stdClass
     */
    public function getAttributeType($code): \stdClass
    {
        return LvDB::table('attribute_types')->where('code_name', $code)->first();
    }

    /**
     * @param array $codes
     *
     * @return Collection
     */
    public function getAttributeTypes(array $codes): Collection
    {
        return LvDB::table('attribute_types')
                    ->whereIn('code_name', $codes)
                    ->get()
                    ->keyBy('code_name');
    }

    public function insertSchemaAttribute(
        $attributeTypeCode,
        $schemaID,
        int $order,
        $label = null
    ): bool {
        return LvDB::table('schema_attributes')->insert([
            'attribute_type_code_name'  => $attributeTypeCode,
            'schema_id'                 => $schemaID,
            'order'                     => $order,
            'label'                     => $label
        ]);
    }

    /**
     * @param mixed $schemaId
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSchemaAttributes($schemaId): Collection
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

    public function insertEntityAttribute(
        $attributableId,
        $attributableType,
        $attributeType,
        $value,
        $valueType = null
    ): ?int {
        $valueType = $valueType ?? $this->getAttributeType($attributeType)->value_type;
        $attributableType = $this->schema::TYPES[$attributableType];

        $valueColumn = self::VALUE_COLUMS[$valueType];

        $id = LvDB::table('attributes')->insertGetId([
            'attributable_id' => $attributableId,
            'attributable_type' => $attributableType,
            'attribute_type_code_name' => $attributeType,
            $valueColumn => $value
        ]);

        return $id;
    }



    /**
     * @param mixed $entityObject the attributable object
     * @param string $entityGenus the attributable type ('source' | 'creator')
     * @param array $attributes code => value
     * @param mixed $userID
     * @param null $updated
     * @param null $created
     *
     * @return int id of the new entity
     */
    public function insertEntityAttributes(
        Attributable $entityObject,
        array $attributes,
        $userID,
        $updated = null,
        $created = null
    ): int {
        $updated = $updated ?? now();
        $created = $created ?? now();
        $entityID = 0;
        $this->db->transaction(function () use (
            $updated,
            $created,
            $userID,
            $entityObject,
            $attributes,
            &$entityID
        ) {
            $entityTable = $entityObject->genus() . 's';
            // insert entity entry
            $entityID = $this->db->table($entityTable)->insertGetId([
                'updated_at' => $updated,
                'created_at' => $created,
                $this->logos->getUsersTableData()->FK => $userID,
                $entityObject->genus() . '_type_code_name' => $entityObject->typeCode()
            ]);
            $entityObject->fill([
                'id' => $entityID
            ]);

            // insert entity attributes
            $data = [];
            $baseRow = [
                'attributable_id'           => $entityID,
                'attributable_type'         => $this->schema::TYPES[$entityObject->genus()],
                'attribute_type_code_name'  => null,
                'text_value'                => null,
                'number_value'              => null,
                'date_value'                => null,
                'complex_value'             => null
            ];
            $type = $entityObject->type();
            foreach ($attributes as $code => $value) {
                $data[] = array_merge(
                    $baseRow,
                    [
                    'attribute_type_code_name'                  => $code,
                    self::VALUE_COLUMS[$type->$code->type]      => $value,
                    ]
                );
                $entityObject->pushAttribute($code, $value);
            }
            $this->db
                ->table('attributes')
                ->insert($data);
        });
        return $entityID;
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Support\Collection¬
     */
    public function getEntityAttributes(int $id, $attributableType = 'source'): Collection
    {
        $valueColumns = $this::VALUE_COLUMS;
        $attributableType = $this->schema::TYPES[$attributableType];
        $entityTable = $attributableType . 's';
        return LvDB::table($entityTable)
            ->join(
                'attributes',
                $entityTable . '.id',
                '=',
                'attributes.attributable_id'
            )
            ->join(
                'attribute_types',
                'attributes.attribute_type_code_name',
                '=',
                'attribute_types.code_name'
            )
            ->where('attributable_type', $attributableType)
            ->where('attributable_id', $id)
            ->select([$entityTable . '.*', 'attributes.*', 'attribute_types.value_type'])
            ->get()
            ->keyBy('attribute_type_code_name')
            ->map(function ($item) use ($valueColumns) {
                $item->value = $item->{$valueColumns[$item->value_type]};
                return $item;
            });
    }

    public function insertCreatorType($code, $label = null): bool
    {
        return LvDB::table('creator_types')->insert([
            'code_name' => $code,
            'label'     => $label
        ]);
    }

    public function roleExist($code): bool
    {
        return LvDB::table('roles')->where('code_name', $code)->exists();
    }

    public function insertRole($code, bool $primary = false): bool
    {
        return LvDB::table('roles')->insert([
            'code_name' => $code,
            'primary'   => $primary
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

    public function insertParticipationType($sourceTypeCode, $roleCode): bool
    {
        return LvDB::table('participation_types')->insert([
            'source_type_code_name' => $sourceTypeCode,
            'role_code_name'        => $roleCode
        ]);
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

    public function getSourceType($codeName)
    {
        return LvDB::table('source_types')->where([
            'code_name' => $codeName,
        ])->first();
    }

    public function getSource($id)
    {
        return LvDB::table('sources')->find($id);
    }

    /**
     * @param mixed $type
     * @param mixed $userId
     * @param null $updated
     * @param null $created
     *
     * @return int id of the source
     */
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

    public function getCreatorSchema($codename)
    {
        return $this->getSchema($codename, $this->schema::TYPES['creator']);
    }

    public function getCreatorType($codeName)
    {
        return LvDB::table('creator_types')->where([
            'code_name' => $codeName,
        ])->first();
    }

    /**
     * @param mixed $type
     * @param mixed $userId
     * @param null $updated
     * @param null $created
     *
     * @return int id of the creator
     */
    public function insertCreator($type, $userId, $updated = null, $created = null): int
    {
        $updated = $updated ?? now();
        $created = $created ?? now();
        return LvDB::table('creators')->insertGetId([
            'updated_at' => $updated,
            'created_at' => $created,
            $this->logos->getUsersTableData()->FK => $userId,
            'creator_type_code_name' => $type
        ]);
    }

    public function getCreator($id)
    {
        return LvDB::table('creators')->find($id);
    }
}
