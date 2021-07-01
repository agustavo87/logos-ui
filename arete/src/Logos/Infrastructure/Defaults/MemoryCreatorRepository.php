<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Defaults;

use Arete\Logos\Application\Ports\Interfaces\CreatorsRepository;
use Arete\Logos\Application\Ports\Interfaces\CreatorTypeRepository;
use Arete\Logos\Application\Ports\Interfaces\LogosEnviroment;
use Arete\Logos\Domain\Creator;

class MemoryCreatorRepository implements CreatorsRepository
{
    protected CreatorTypeRepository $creatorTypes;
    protected LogosEnviroment $logos;

    /**
     * @var Creator[]
     */
    protected static array $creators = [];
    protected static array $ids = [0];

    public function __construct(
        CreatorTypeRepository $creatorTypes,
        LogosEnviroment $logos
    ) {
        $this->creatorTypes = $creatorTypes;
        $this->logos = $logos;
    }

    /**
     * Creates a new Creator from an array of
     * property => value
     *
     * @param array $properties
     * @param $ownerID
     *
     * @throws IncorrectDataStructureException
     * @return void
     */
    public function createFromArray(array $params, $ownerID = null): Creator
    {
        $ownerID = $ownerID ?? $this->logos->getOwner();
        $entityID = $this->newId();

        $creator = new Creator(
            $this->creatorTypes,
            [
                'id'        => $entityID,
                'ownerID'   => $ownerID,
                'typeCode'  => $params['type']
            ]
        );

        if ($params['attributes']) {
            foreach ($params['attributes'] as $code => $value) {
                $creator->pushAttribute($code, $value);
            }
        }

        self::$creators[$entityID] = $creator;

        return $creator;
    }

    protected static function newId(): int
    {
        $id = array_slice(self::$ids, -1, 1)[0] + 1;
        self::$ids[] = $id;
        return $id;
    }

    /**
     * Returns the creator of provided id, null otherwise
     *
     * @param int $id
     *
     * @return Creator|null
     */
    public function get(int $id): ?Creator
    {
        return self::$creators[$id];
    }

    /**
     * @param Creator $creator
     *
     * @throws PersistenceException
     * @return bool true if sucessful
     */
    public function save(Creator $creator): bool
    {
        // althought it is automatically saved becouse all variables
        // just references the same object. It may have been cloned.
        self::$creators[$creator->id()] = $creator;
        return true;
    }

    /**
     * Give the creators who matches the specified criteria
     *
     * @param string    $attributeCode
     * @param string    $attributeValue
     * @param mixed     $ownerID = null           the owner of the creators.
     *
     * @return array
     */
    public function getLike(string $attributeCode, string $attributeValue, $ownerID = null): array
    {
        $results =  array_filter(self::$creators, function (Creator $creator) use (
            $attributeValue,
            $attributeCode,
            $ownerID
        ) {
            if ($ownerID) {
                if ($ownerID != $creator->ownerID()) {
                    return false;
                }
            }
            return str_contains((string) $creator->$attributeCode, $attributeValue);
        });
        return $results;
    }

    /**
     * returns a new object even if theres already an instance of it
     *
     * the parallel versions can create unexpected results.
     *
     * @param int $id
     *
     * @return Creator|null
     */
    public function getNew(int $id): ?Creator
    {
        return clone self::$creators[$id];
    }
}
