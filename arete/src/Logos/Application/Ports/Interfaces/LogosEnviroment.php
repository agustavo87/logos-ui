<?php

declare(strict_types=1);

namespace Arete\Logos\Application\Ports\Interfaces;

interface LogosEnviroment
{
    /**
     * Returns info about table name and key of owner in a relational
     * DB table.
     *
     * @return \stdClass
     */
    public function getOwnersTableData(): \stdClass;

    /**
     * Sets the default owner of the ownerables entitiles
     *
     * @param string $id
     *
     * @return void
     */
    public function setOwner(string $id);

    /**
     * Returns the default owner of the ownerables entitiles
     *
     * @param string $id
     *
     * @return void
     */
    public function getOwner();
}
