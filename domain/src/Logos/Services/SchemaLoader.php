<?php

declare(strict_types=1);

namespace Arete\Logos\Services;

use Arete\Logos\Interfaces\SchemaLoaderInterface;
use Arete\Logos\Models\Zotero\Schema;

class SchemaLoader implements SchemaLoaderInterface
{
    protected $loaders = [
        'simpleZotero' => \Arete\Logos\Services\Zotero\SimpleSchemaLoader::class
    ];

    protected $default = 'simpleZotero';

    public function load(?string $schema = null): Schema
    {
        $schema = $schema ?? $this->default;
        return (new $this->loaders[$schema]())->load();
    }

    public function __invoke(?string $schema = null)
    {
        return $this->load($schema);
    }
}
