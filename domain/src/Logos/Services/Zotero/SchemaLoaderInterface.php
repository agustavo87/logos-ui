<?php

declare(strict_types=1);

namespace Arete\Logos\Services\Zotero;

use Arete\Logos\Models\Zotero\Schema;

interface SchemaLoaderInterface
{
    public function load(): Schema;
}
