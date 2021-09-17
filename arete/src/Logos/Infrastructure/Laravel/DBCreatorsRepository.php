<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Laravel;

use Arete\Logos\Application\Ports\Interfaces\CreatorsRepository;
use Arete\Logos\Infrastructure\Laravel\Common\DBRepository;
use Arete\Logos\Application\Ports\Interfaces\CreatorTypeRepository;
use Arete\Logos\Application\Ports\Interfaces\LogosEnviroment;
use Arete\Logos\Infrastructure\Laravel\Common\DB;
use Arete\Logos\Domain\Creator;

class DBCreatorsRepository extends DBRepository implements CreatorsRepository
{
    protected CreatorTypeRepository $creatorTypes;
    protected int $maxFetchSize = 30;
    protected array $cache = [];

    public function __construct(
        DB $db,
        LogosEnviroment $env,
        CreatorTypeRepository $creatorTypes
    ) {
        parent::__construct($db, $env);
        $this->creatorTypes = $creatorTypes;
    }

    /**
     * @param array $params
     * @param null $ownerID
     *
     * @throws \Arete\Exceptions\PersistenceException
     * @throws \Arete\Exceptions\IncorrectDataStructureException
     * @return Creator
     */
    public function createFromArray(array $params, $ownerID = null): Creator
    {
        $ownerID = $ownerID ?? $this->env->getOwner();
        $creator = new Creator(
            $this->creatorTypes,
            [
                'typeCode'  => $params['type'],
                'ownerID' => $ownerID
            ]
        );

        $this->db->insertEntityAttributes(
            $creator,
            $params['attributes']
        );

        return $creator;
    }

    public function get(int $id): ?Creator
    {
        // use the cache to not return different objects
        if (array_key_exists($id, $this->cache)) {
            return $this->cache[$id];
        }
        return $this->getNew($id);
    }

    /**
     * returns a new object even if theres already an instance of it
     *
     * Don't return from the cache if there is one. Has to be used carrefully
     * the parallel versions can create unexpected results.
     *
     * @param int $id
     *
     * @return Creator|null
     */
    public function getNew(int $id): ?Creator
    {
        $ownerFKColumn = $this->env->getOwnersTableData()->FK;

        $attributes = $this->db->getEntityAttributes($id, 'creator');
        $creatorEntry = $attributes->first();
        $creator = new Creator(
            $this->creatorTypes,
            [
                'id'        => $creatorEntry->id,
                'typeCode'  => $creatorEntry->creator_type_code_name,
                'ownerID'   => $creatorEntry->$ownerFKColumn
            ]
        );
        foreach ($attributes as $code => $data) {
            $creator->pushAttribute(
                $code,
                $data->value
            );
        }
        return $this->cache[$id] = $creator;
    }

    public function save(Creator $creator): bool
    {
        return (bool) $this->db->insertAttributes(
            $creator->id(),
            $creator->type(),
            $creator->getDirtyAttributes()
        );
    }

    public function getLike($attributeCode, $attributeValue, $ownerID = null, $page = null): array
    {
        $entitiesIDs = $this->db->findEntitiesWith(
            'creator',
            $attributeCode,
            $attributeValue,
            $ownerID
        );

        $result = [];
        $take = count($entitiesIDs) > $this->maxFetchSize ? $this->maxFetchSize : count($entitiesIDs);
        for ($i = 0; $i <= $take - 1; $i++) {
            $creator = $this->get($entitiesIDs[$i]);
            $result[] = $creator;
        }

        return $result;
    }

    public function suggestCreators(
        $owner,
        string $hint,
        string $attribute = 'lastName',
        string $type = 'person',
        string $orderBy = 'lastName',
        bool $asc = true,
        int $limit = 5
    ): array {
        $data = $this->db->suggestCreators(
            $owner,
            $hint,
            $attribute,
            $type,
            $orderBy,
            $asc,
            $limit
        );
        $result = $data->reduce(function ($items, $item) {
            if (!isset($items[$item->id])) {
                $items[$item->id] = [];
            }
            $items[$item->id][$item->attribute] = $item->value;
            return $items;
        }, []);
        // dd($result);
        return $result;
    }
}
