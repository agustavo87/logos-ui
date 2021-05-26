<?php

declare(strict_types=1);

namespace Arete\Logos\Services\Zotero;

use Arete\Logos\Models\Zotero\{CreatorType, Schema, ItemType, Field};

class SimpleSchemaLoader implements SchemaLoaderInterface
{
    public function load(): Schema
    {
        $itemType = new ItemType([
            'itemType'  => 'journalArticle',
            'fields'    => [
                new Field(['field' => 'title'])
            ],
            'creatorTypes' => [
                new CreatorType([
                    'creatorType' => 'author',
                    'primary' => true
                ])
            ]
        ]);

        $schema = new Schema([
            'version' => 1,
            'itemTypes' => [$itemType],
        ]);

        return $schema;
    }
}
