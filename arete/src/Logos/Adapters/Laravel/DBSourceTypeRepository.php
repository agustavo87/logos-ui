<?php

declare(strict_types=1);

namespace Arete\Logos\Adapters\Laravel;

use Arete\Logos\Ports\Interfaces\SourceTypeRepository as SourceTypeRepositoryPort;
use Arete\Logos\Models\SourceType;
use Arete\Logos\Models\Laravel\LvSourceType;
use Arete\Logos\Adapters\Laravel\Common\DBRepository;

/**
 * Laravel dependent Data Base based Source Type Repository
 */
class DBSourceTypeRepository extends DBRepository implements SourceTypeRepositoryPort
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
