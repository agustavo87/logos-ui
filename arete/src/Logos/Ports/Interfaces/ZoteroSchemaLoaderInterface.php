<?php

declare(strict_types=1);

namespace Arete\Logos\Ports\Interfaces;

use Arete\Logos\Models\Zotero\ZoteroSchema;

interface ZoteroSchemaLoaderInterface
{
    public function load(?string $schema = null): ZoteroSchema;
}
