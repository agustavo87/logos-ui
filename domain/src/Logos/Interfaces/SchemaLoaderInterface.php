<?php

declare(strict_types=1);

namespace Arete\Logos\Interfaces;

use Arete\Logos\Models\Zotero\Schema;

interface SchemaLoaderInterface
{
    public function load(?string $schema): Schema;
}
