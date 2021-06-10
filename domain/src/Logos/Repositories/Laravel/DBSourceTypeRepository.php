<?php

declare(strict_types=1);

namespace Arete\Logos\Repositories\Laravel;

use Arete\Logos\Repositories\SourceTypeRepositoryInterface;
use Arete\Logos\Models\SourceType;
use Arete\Logos\Models\Laravel\LvSourceType;
use Arete\Logos\Repositories\Laravel\Conceptual\DBRepository;

/**
 * Laravel dependent Data Base based Source Type Repository
 */
class DBSourceTypeRepository extends DBRepository implements SourceTypeRepositoryInterface
{
    public function get($codeName): SourceType
    {
        $schema = $this->db->getSourceSchema($codeName);
        return LvSourceType::fromLvData(
            $this->db->getSourceType($codeName),
            $schema,
            $this->db->getAttributes($schema->id),
            $this->db->getRoles($codeName)
        );
    }
}
