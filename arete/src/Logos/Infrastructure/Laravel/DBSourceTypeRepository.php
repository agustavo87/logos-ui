<?php

/**
 * @todo this could be cached, becouse it remains the same most of the time.
 */

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Laravel;

use Arete\Logos\Application\Ports\Interfaces\SourceTypeRepository;
use Arete\Logos\Domain\Abstracts\SourceType;
use Arete\Logos\Infrastructure\Laravel\Models\LvSourceType;
use Arete\Logos\Infrastructure\Laravel\Common\DBRepository;

/**
 * Laravel dependent Data Base based Source Type Repository
 */
class DBSourceTypeRepository extends DBRepository implements SourceTypeRepository
{
    public function get($codeName): SourceType
    {
        $schema = $this->db->getSourceSchema($codeName);
        return LvSourceType::fromLvData(
            $this->db->getSourceType($codeName),
            $schema,
            $this->db->getSchemaAttributes($schema->id),
            $this->db->getRoles($codeName)
        );
    }

    public function types(): array
    {
        return $this->db
                    ->getSourceTypeNames()
                    ->map(fn ($obj) => $obj->code_name)
                    ->toArray();
    }

    public function attributes(?string $type = null): array
    {
        return $this->db->getSourceTypeAttributes($type)
                 ->map(fn ($obj) => $obj->attribute_type_code_name)
                 ->toArray();
    }
}
