<?php

declare(strict_types=1);

namespace Arete\Logos\Application\Ports\Interfaces;

interface LogosEnviroment
{
    public function getUsersTableData(): \stdClass;

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
