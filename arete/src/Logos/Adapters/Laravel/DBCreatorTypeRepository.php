<?php

declare(strict_types=1);

namespace Arete\Logos\Adapters\Laravel;

use Arete\Logos\Ports\Interfaces\CreatorTypeRepository;
use Arete\Logos\Models\CreatorType;
use Arete\Logos\Adapters\Laravel\Models\LvCreatorType;
use Arete\Logos\Adapters\Laravel\Common\DBRepository;

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
