<?php

declare(strict_types=1);

namespace Arete\Logos\Services\Zotero;

use Arete\Logos\Ports\Interfaces\ZoteroSchemaLoaderInterface;
use Arete\Logos\Domain\Zotero\ZoteroSchema;

class ZoteroSchemaLoader implements ZoteroSchemaLoaderInterface
{
    protected $loaders = [
        'simple' => \Arete\Logos\Services\Zotero\SimpleSchemaLoader::class
    ];

    protected $default = 'simple';

    public function load(?string $schema = null): ZoteroSchema
    {
        $schema = $schema ?? $this->default;
        return (new $this->loaders[$schema]())->load();
    }

    public function __invoke(?string $schema = null)
    {
        return $this->load($schema);
    }
}
