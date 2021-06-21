<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Laravel;

use Arete\Logos\Ports\Interfaces\CreatorTypeRepository;
use Arete\Logos\Domain\Abstracts\CreatorType;
use Arete\Logos\Infrastructure\Laravel\Models\LvCreatorType;
use Arete\Logos\Infrastructure\Laravel\Common\DBRepository;

/**
 * Laravel dependent Data Base based Creator Type Repository
 */
class DBCreatorTypeRepository extends DBRepository implements CreatorTypeRepository
{
    public function get($codeName): CreatorType
    {
        $schema = $this->db->getCreatorSchema($codeName);
        return LvCreatorType::fromLvData(
            $this->db->getCreatorType($codeName),
            $schema,
            $this->db->getSchemaAttributes($schema->id),
        );
    }
}
