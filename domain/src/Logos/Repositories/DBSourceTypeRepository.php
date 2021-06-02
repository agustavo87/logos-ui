<?php

declare(strict_types=1);

namespace Arete\Logos\Repositories;

use Arete\Logos\Models\SourceType;
use Illuminate\Support\Facades\DB;

class DBSourceTypeRepository implements SourceTypeRepositoryInterface
{

    public function get($codeName): SourceType
    {
        $schema = $this->getSchema($codeName);

        /**
         * @todo asegurarse de no pasar ningún tipo específico de
         * laravel al dominio.
         * */
        return new SourceType(
            $this->getSourceType($codeName),
            $schema,
            $this->getAttributes($schema->id),
            $this->getRoles($codeName)
        );
    }

    protected function getRoles($codeName)
    {
        return DB::table('participation_types')
            ->join('roles', 'participation_types.role_code_name', '=', 'roles.code_name')
            ->select('roles.*')
            ->where('participation_types.source_type_code_name', $codeName)
            ->get();
    }

    protected function getSchema($codeName)
    {
        return DB::table('schemas')
            ->where('type_code_name', $codeName)
            ->where('type', config('logos.schemaTypes.source'))
            ->latest()
            ->first();
    }

    protected function getSourceType($codeName)
    {
        return DB::table('source_types')->where([
            'code_name' => $codeName,
        ])->first();
    }

    protected function getAttributes($schemaId)
    {
        return DB::table('schema_attributes')
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
}
