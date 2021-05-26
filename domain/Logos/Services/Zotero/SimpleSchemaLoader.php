<?php

declare(strict_types=1);

namespace Arete\Logos\Services\Zotero;

use Arete\Logos\Models\Zotero\Schema;

class SimpleSchemaLoader implements SchemaLoaderInterface
{
    public function load(): Schema
    {
        return (new Schema([
            'version' => 1,
        ]))->addItemTypes([
            [
                'itemType' => 'journalArticle',
                'fields' => [
                    ['field' => 'title']
                ],
                'creatorTypes' => [
                    [
                        'creatorType' => 'author',
                        'primary' => true
                    ]
                ]
            ]
        ]);
    }
}
