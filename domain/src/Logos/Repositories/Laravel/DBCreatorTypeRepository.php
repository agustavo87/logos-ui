<?php

declare(strict_types=1);

namespace Arete\Logos\Repositories\Laravel;

use Arete\Logos\Repositories\CreatorTypeRepositoryInterface;
use Arete\Logos\Models\CreatorType;
use Arete\Logos\Models\Laravel\LvCreatorType;
use Arete\Logos\Repositories\Laravel\Partials\DBRepository;

/**
 * Laravel dependent Data Base based Creator Type Repository
 */
class DBCreatorTypeRepository extends DBRepository implements CreatorTypeRepositoryInterface
{
    public function get($codeName): CreatorType
    {
        $schema = $this->db->getCreatorSchema($codeName);
        return LvCreatorType::fromLvData(
            $this->db->getCreatorType($codeName),
            $schema,
            $this->db->getAttributes($schema->id),
        );
    }
}
