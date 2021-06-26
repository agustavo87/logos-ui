<?php

declare(strict_types=1);

namespace Arete\Logos\Application\Ports\Interfaces;

use Arete\Logos\Domain\Zotero\ZoteroSchema;

interface ZoteroSchemaLoaderInterface
{
    public function load(?string $schema = null): ZoteroSchema;
}
