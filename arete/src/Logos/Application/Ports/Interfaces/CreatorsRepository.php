<?php

declare(strict_types=1);

namespace Arete\Logos\Application\Ports\Interfaces;

use Arete\Logos\Domain\Creator;
use Arete\Common\Exceptions\IncorrectDataStructureException;
use Arete\Common\Exceptions\PersistenceException;

interface CreatorsRepository
{
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
    public function createFromArray(array $attributes, $ownerID = null): Creator;

    /**
     * Returns the creator of provided id, null otherwise
     *
     * @param int $id
     *
     * @return Creator|null
     */
    public function get(int $id): ?Creator;

    /**
     * @param Creator $creator
     *
     * @throws PersistenceException
     * @return bool true if sucessful
     */
    public function save(Creator $creator): bool;

    /**
     * Give the creators who matches the specified criteria
     *
     * @param string    $attributeCode
     * @param string    $attributeValue
     * @param mixed     $ownerID = null           the owner of the creators.
     *
     * @return Creator[]
     */
    public function getLike(string $attributeCode, string $attributeValue, $ownerID = null): array;

    /**
     * returns a new object even if theres already an instance of it
     *
     * the parallel versions can create unexpected results.
     *
     * @param int $id
     *
     * @return Creator|null
     */
    public function getNew(int $id): ?Creator;
}
