<?php

declare(strict_types=1);

namespace Arete\Logos\Ports\Interfaces;

use Arete\Logos\Models\Creator;
use Arete\Common\Exceptions\IncorrectDataStructureException;
use Arete\Common\Exceptions\PersistenceException;

interface CreatorsRepository
{
    /**
     * Creates a new Creator from an array of
     * property => value
     *
     * @param array $properties
     *
     * @throws IncorrectDataStructureException
     * @return void
     */
    public function createFromArray(array $attributes, $userId): Creator;

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
     * @param int   $user       the owner of the creators.
     * @param array $criteria   a property => value array of criteria to look up.
     *
     * @return array
     */
    public function getLike(int $user, array $criteria): array;
}
