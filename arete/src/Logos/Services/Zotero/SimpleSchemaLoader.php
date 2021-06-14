<?php

declare(strict_types=1);

namespace Arete\Logos\Services\Zotero;

use Arete\Logos\Models\Zotero\ZoteroSchema;
use Arete\Logos\Interfaces\ZoteroSchemaLoaderInterface;

class SimpleSchemaLoader implements ZoteroSchemaLoaderInterface
{
    public function load(?string $schema = null): ZoteroSchema
    {
        $schema = new ZoteroSchema([
            'version' => 1,
            'meta' => [
                'fields' => [
                    'date' => [
                        'type' => 'date'
                    ],
                    'fillingDate' => [
                        'type' => 'date'
                    ]
                ]
            ]
        ]);

        $schema->addItemTypes([
            [
                'itemType' => 'annotation'
            ],[
                'itemType' => 'blogPost',
                'fields' => [
                    ['field' => 'title'],
                    ['field' => 'abstractNote'],
                    ['field' => 'blogTitle', 'baseField' => 'publicationTitle'],
                    ['field' => 'websiteType', 'baseField' => 'type'],
                    ['field' => 'date'],
                    ['field' => 'url'],
                    ['field' => 'accessDate'],
                    ['field' => 'language'],
                    ['field' => 'shortTitle'],
                    ['field' => 'rights'],
                    ['field' => 'extra'],
                ],
                'creatorTypes' => [
                    ['creatorType' => 'author', 'primary' => true],
                    ['creatorType' => 'commenter'],
                    ['creatorType' => 'contributor']
                ]
            ],[
                'itemType' => 'book',
                'fields' => [
                    ['field' => 'title'],
                    ['field' => 'abstractNote'],
                    ['field' => 'series'],
                    ['field' => 'seriesNumber'],
                    ['field' => 'volume'],
                    ['field' => 'numberOfVolumes'],
                    ['field' => 'edition'],
                    ['field' => 'place'],
                    ['field' => 'publisher'],
                    ['field' => 'date'],
                    ['field' => 'numPages'],
                    ['field' => 'language'],
                    ['field' => 'ISBN'],
                    ['field' => 'shortTitle'],
                    ['field' => 'url'],
                    ['field' => 'accessDate'],
                    ['field' => 'archive'],
                    ['field' => 'archiveLocation'],
                    ['field' => 'libraryCatalog'],
                    ['field' => 'callNumber'],
                    ['field' => 'rights'],
                    ['field' => 'extra'],
                ],
                'creatorTypes' => [
                    ['creatorType' => 'author', 'primary' => true],
                    ['creatorType' => 'contributor'],
                    ['creatorType' => 'editor'],
                    ['creatorType' => 'translator'],
                    ['creatorType' => 'seriesEditor'],
                ]
            ],[
                'itemType' => 'bookSection',
                'fields' => [
                    ['field' => 'title'],
                    ['field' => 'abstractNote'],
                    ['field' => 'bookTitle', 'baseField' => 'publicationTitle'],
                    ['field' => 'series'],
                    ['field' => 'seriesNumber'],
                    ['field' => 'volume'],
                    ['field' => 'numberOfVolumes'],
                    ['field' => 'edition'],
                    ['field' => 'place'],
                    ['field' => 'publisher'],
                    ['field' => 'date'],
                    ['field' => 'pages'],
                    ['field' => 'language'],
                    ['field' => 'ISBN'],
                    ['field' => 'shortTitle'],
                    ['field' => 'url'],
                    ['field' => 'accessDate'],
                    ['field' => 'archive'],
                    ['field' => 'archiveLocation'],
                    ['field' => 'libraryCatalog'],
                    ['field' => 'callNumber'],
                    ['field' => 'rights'],
                    ['field' => 'extra'],
                ],
                'creatorTypes' => [
                    ['creatorType' => 'author', 'primary' => true],
                    ['creatorType' => 'contributor'],
                    ['creatorType' => 'editor'],
                    ['creatorType' => 'bookAuthor'],
                    ['creatorType' => 'translator'],
                    ['creatorType' => 'seriesEditor'],
                ]
            ],[
                'itemType' => 'journalArticle',
                'fields' => [
                    ['field' => 'title'],
                    ['field' => 'abstractNote'],
                    ['field' => 'publicationTitle'],
                    ['field' => 'volume'],
                    ['field' => 'issue'],
                    ['field' => 'pages'],
                    ['field' => 'date'],
                    ['field' => 'series'],
                    ['field' => 'seriesTitle'],
                    ['field' => 'seriesText'],
                    ['field' => 'journalAbbreviation'],
                    ['field' => 'language'],
                    ['field' => 'DOI'],
                    ['field' => 'ISSN'],
                    ['field' => 'shortTitle'],
                    ['field' => 'url'],
                    ['field' => 'accessDate'],
                    ['field' => 'archive'],
                    ['field' => 'archiveLocation'],
                    ['field' => 'libraryCatalog'],
                    ['field' => 'callNumber'],
                    ['field' => 'rights'],
                    ['field' => 'extra'],
                ],
                'creatorTypes' => [
                    ['creatorType' => 'author', 'primary' => true],
                    ['creatorType' => 'contributor'],
                    ['creatorType' => 'editor'],
                    ['creatorType' => 'translator'],
                    ['creatorType' => 'reviewedAuthor'],
                ]
            ],
        ], true);

        return $schema;
    }
}
