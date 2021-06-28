<?php

/** @todo reemplazar accesos por LvDB facade */

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Laravel\Common;

use Arete\Logos\Domain\Abstracts\Attributable;
use Arete\Logos\Domain\Abstracts\Type;
use Arete\Logos\Domain\Schema;
use Arete\Logos\Application\Ports\Interfaces\LogosEnviroment;
use Arete\Exceptions\PersistenceException;
use Arete\Logos\Domain\Creator;
use Arete\Logos\Domain\Role;
use Arete\Logos\Domain\Source;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB as LvDB;
use Illuminate\Support\Facades\Log;

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
        $attributableGenus,
        $attributeType,
        $value,
        $valueType = null
    ): ?int {
        $valueType = $valueType ?? $this->getAttributeType($attributeType)->value_type;
        $attributableGenus = $this->schema::GENUS[$attributableGenus];

        $valueColumn = self::VALUE_COLUMS[$valueType];

        $id = LvDB::table('attributes')->insertGetId([
            'attributable_id' => $attributableId,
            'attributable_genus' => $attributableGenus,
            'attribute_type_code_name' => $attributeType,
            $valueColumn => $value
        ]);

        return $id;
    }

    /**
     * @param mixed $attributableID
     * @param Type $entityType
     * @param array $attributes [code => value, ...]
     *
     * @return int
     */
    public function insertAttributes($attributableID, Type $entityType, array $attributes): int
    {
        $entityGenus = $entityType->genus();
        $data = [];
            $baseRow = [
                'attributable_id'           => $attributableID,
                'attributable_genus'         => $this->schema::GENUS[$entityGenus],
                'attribute_type_code_name'  => null,
                'text_value'                => null,
                'number_value'              => null,
                'date_value'                => null,
                'complex_value'             => null
            ];
            foreach ($attributes as $code => $value) {
                $data[] = array_merge(
                    $baseRow,
                    [
                    'attribute_type_code_name'                      => $code,
                    self::VALUE_COLUMS[$entityType->$code->type]    => $value,
                    ]
                );
                // $entityObject->pushAttribute($code, $value);
            }
            return $this->db
                ->table('attributes')
                ->upsert(
                    $data,
                    ['attributable_id', 'attributable_genus', 'attribute_type_code_name'],
                    ['text_value', 'number_value', 'date_value', 'complex_value']
                );
    }

    /**
     * @param mixed $entityObject the attributable object
     * @param array $attributes code => value
     * @param mixed $userID
     * @param null $updated
     * @param null $created
     *
     * @throws \Arete\Exceptions\PersistenceException
     * @return int|null id of the new entity or null if error.
     */
    public function insertEntityAttributes(
        Attributable $entityObject,
        array $attributes,
        $updated = null,
        $created = null
    ): ?int {
        $updated = $updated ?? now();
        $created = $created ?? now();
        $entityTable = $entityObject->genus() . 's';
        $type = $entityObject->type();

        try {
            $this->db->beginTransaction();

            // insert entity entry
            $ownerColumn = $this->logos->getOwnersTableData()->FK;
            $entityID = $this->db->table($entityTable)->insertGetId([
                'updated_at' => $updated,
                'created_at' => $created,
                $ownerColumn => $entityObject->ownerID(),
                $entityObject->genus() . '_type_code_name' => $entityObject->typeCode()
            ]);
            $entityObject->fill([
                'id' => $entityID
            ]);

            // insert entity attributes
            $this->insertAttributes($entityID, $type, $attributes);

            $this->db->commit();

            // if everything is ok
            foreach ($attributes as $code => $value) {
                $entityObject->pushAttribute($code, $value);
            }
            return $entityID;
        } catch (\Throwable $th) {
            $this->db->rollBack();
            Log::error('Error in inserting entity attributes', ['throwable' => $th]);
            throw new PersistenceException('Error in inserting entity attributes', 0, $th);
        }
        return null;
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Support\Collection¬
     */
    public function getEntityAttributes(int $id, $attributableGenus = 'source'): Collection
    {
        $valueColumns = $this::VALUE_COLUMS;
        $attributableGenus = $this->schema::GENUS[$attributableGenus];
        $entityTable = $this->getEntityTable($attributableGenus);
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
            ->where('attributable_genus', $attributableGenus)
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
        return $this->getSchema($codename, $this->schema::GENUS['source']);
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
            $this->logos->getOwnersTableData()->FK => $userId,
            'source_type_code_name' => $type
        ]);
    }

    public function getCreatorSchema($codename)
    {
        return $this->getSchema($codename, $this->schema::GENUS['creator']);
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
            $this->logos->getOwnersTableData()->FK => $userId,
            'creator_type_code_name' => $type
        ]);
    }

    public function getCreator($id)
    {
        return LvDB::table('creators')->find($id);
    }

    public function getEntityTable(string $attributableGenus)
    {
        return $attributableGenus . 's';
    }

    public function findEntitiesWith(
        string $attributableGenus,
        string $attributeCode,
        string $attributeValue,
        $ownerID = null
    ): array {
        $valueType = $this->db->table('attribute_types')
            ->where('code_name', $attributeCode)
            ->select('value_type')
            ->first()
            ->value_type;

        $entityTable = $this->getEntityTable($attributableGenus);
        $query = $this->db->table($entityTable)
            ->join(
                'attributes',
                $entityTable . '.id',
                '=',
                'attributes.attributable_id'
            )
            ->where('attributable_genus', $attributableGenus)
            ->where('attribute_type_code_name', $attributeCode)
            ->where($this::VALUE_COLUMS[$valueType], 'LIKE', '%' . $attributeValue . '%');
        if ($ownerID) {
            $query->where(
                $this->logos->getOwnersTableData()->FK,
                $ownerID
            );
        }
        $IDs = $query->select($entityTable . '.id')
                     ->get();

        return $IDs->map(fn ($entry) => $entry->id)->toArray();
    }

    public function insertParticipation(
        Source $source,
        Creator $creator,
        Role $role,
        int $relevance
    ): int {
        return $this->db->table('participations')
            ->upsert(
                [
                    'source_id' => $source->id(),
                    'creator_id' => $creator->id(),
                    'role_code_name' => $role->code,
                    'relevance' => $relevance
                ],
                ['source_id', 'creator_id', 'role_code_name'],
                ['relevance']
            );
    }

    /**
     * @param Participation[] $participationsData
     *
     * @return int
     */
    public function saveParticipations($participationsData): int
    {
        $preparedData = [];
        foreach ($participationsData as $participation) {
            $preparedData[] = [
                'source_id' => $participation->source()->id(),
                'creator_id' => $participation->creatorId(),
                'role_code_name' => $participation->role()->code,
                'relevance' => $participation->relevance()
            ];
        }
        return $this->db->table('participations')
            ->upsert(
                $preparedData,
                ['source_id', 'creator_id', 'role_code_name'],
                ['relevance']
            );
    }

    public function removeParticipation(Source $source, $roleCode, $creatorID): int
    {
        return $this->db->table('participations')
                ->where([
                    ['source_id', '=', $source->id()],
                    ['creator_id', '=', $creatorID],
                    ['role_code_name', '=', $roleCode]
                ])
                ->delete();
    }

    /**
     * @param Source $source
     *
     * @return Collection
     */
    public function getParticipations(Source $source): Collection
    {
        return $this->db->table('participations')
            ->where(['source_id' => $source->id()])
            ->get();
    }
}
