<?php

declare(strict_types=1);

namespace Arete\Logos\Services\Zotero;

use Arete\Logos\Models\Zotero\{CreatorType, CSLMap, Schema, ItemType, Field};

class SimpleSchemaLoader implements SchemaLoaderInterface
{
    public function load(): Schema
    {
        $itemType = new ItemType();

        $itemType->itemType = 'journalArticle';

        $firstField = $itemType->fields[0] = new Field();

        $firstField->field = 'title';

        $firstCreatorType = $itemType->creatorTypes[0] = new CreatorType();

        $firstCreatorType->creatorType = 'author';
        $firstCreatorType->primary = true;

        $schema = new Schema([
            'version' => 1,
            'itemTypes' => [$itemType],
        ]);

        return $schema;
    }
}
