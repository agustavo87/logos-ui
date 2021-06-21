<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Laravel;

use Arete\Logos\Ports\Interfaces\SourceTypeRepository;
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
}