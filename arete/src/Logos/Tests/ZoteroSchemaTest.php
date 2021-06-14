<?php

declare(strict_types=1);

namespace Arete\Logos\Tests;

use PHPUnit\Framework\TestCase;
use Tests\LogsInformation;
use Arete\Logos\Models\Zotero\{CreatorType, CSLMap, Field, ItemType, ZoteroSchema};
use Arete\Logos\Tests\Traits\ChecksZoteroSchemaDataStructure;

use function Arete\Common\var_dump_ret;

class ZoteroSchemaTest extends TestCase
{
    use ChecksZoteroSchemaDataStructure;
    use LogsInformation;

    public function testSchemaCreatesWithDefaults()
    {
        $schema = new ZoteroSchema();
        $this->checkSchemaDataStructure($schema);
    }

    public function testSchemaMergesDefaults()
    {
        $schema = new ZoteroSchema([
            'version' => 2,
            'csl' => new CSLMap([
                'types' => [
                    'article' => ['document', 'attachment', 'note'],
                    'article-journal' => ['journalArticle']
                ],
                'fields' => [
                    'text' => [
                        'abstract' => ['abstractNote']
                    ]
                ],
            ])
        ]);

        // print_r($schema->csl);

        $this->checkSchemaDataStructure($schema);
        $this->assertEquals(2, $schema->version);
        $this->assertEquals('journalArticle', $schema->csl->types['article-journal'][0]);
        $this->assertEquals('abstractNote', $schema->csl->fields['text']['abstract'][0]);
        $this->assertIsArray($schema->csl->names);
    }

    public function testItemTypeDefaults()
    {
        $itemType = new ItemType([
            'itemType' => 'book',
            'fields' => [
                new Field([
                    'field' => 'title',
                    'baseField' => 'publicationTitle'
                ]),
                new Field([
                    'field' => 'city'
                ])
            ],
            'creatorTypes' => [
                new CreatorType([
                    'creatorType' => 'author',
                    'primary' => true
                ]),
                new CreatorType([
                    'creatorType' => 'editor'
                ])
            ]
        ]);

        // print_r($itemType->creatorTypes);

        $this->checkItemTypeStructure($itemType);
        $this->assertEquals('book', $itemType->itemType);
        $this->assertEquals('title', $itemType->fields[0]->field);
        $this->assertEquals('city', $itemType->fields[1]->field);
        $this->assertEquals('', $itemType->fields[1]->baseField);
        $this->assertEquals('author', $itemType->creatorTypes[0]->creatorType);
        $this->assertTrue($itemType->creatorTypes[0]->primary);
        $this->assertEquals('editor', $itemType->creatorTypes[1]->creatorType);
        $this->assertFalse($itemType->creatorTypes[1]->primary);
    }

    public function testCreatesDefaultItemType()
    {
        $itemType = new ItemType();
        $this->checkItemTypeStructure($itemType);
    }

    public function checkItemTypeStructure($itemType)
    {
        $this->assertInstanceOf(ItemType::class, $itemType);
        $this->assertIsString($itemType->itemType);
        $this->assertContainsOnlyInstancesOf(Field::class, $itemType->fields);
        $this->assertContainsOnlyInstancesOf(CreatorType::class, $itemType->creatorTypes);
    }

    public function testSchemaAddsMasiveItems()
    {
        $schema = new ZoteroSchema([
            'version' => 2,
            'meta' => [
                'some-key' => 'some-value'
            ]
        ]);
        $typesData = [
            [
                'itemType' => 'journajArticle',
                'fields' => [
                    ['field' => 'title'],
                    ['field' => 'abstractNote'],
                ],
                'creatorTypes' => [
                    ['creatorType' => 'author', 'primary' => true],
                    ['creatorType' => 'contributor']
                ]
            ],[
                'itemType' => 'book',
                'fields' => [
                    ['field' => 'title'],
                    ['field' => 'ISBN'],
                ],
                'creatorTypes' => [
                    ['creatorType' => 'author', 'primary' => true],
                    ['creatorType' => 'editor']
                ]
            ]

        ];
        $schema->addItemTypes($typesData, true);
        // $this->log($schema);
        foreach ($typesData as $typeData) {
            // $this->log("Buscando ItemType: {$typeData['itemType']}");
            $results = array_filter($schema->itemTypes, function (ItemType $item) use ($typeData) {
                // $this->log("analizando {$item->itemType}");
                return $item->itemType == $typeData['itemType'];
            });
            $this->assertGreaterThan(0, count($results));
        }
    }

    public function testGetItemByType()
    {
        $schema = (new ZoteroSchema())->addItemTypes([
            [
                'itemType'  => 'book'
            ],
            [
                'itemType'  => 'journalArticle',
                'fields'    => [
                    ['field' => 'journalTitle', 'baseField' => 'publicationTitle']
                ]
            ]
        ], true);
        $journalType = $schema->getItemType('journalArticle');
        $this->assertEquals('journalArticle', $journalType->itemType);
    }

    public function testGetFieldByCode()
    {
        $itemType = new ItemType([
            'itemType'  => 'blogPost',
            'fields'    => [
                new Field(['field' => 'title']),
                new Field(['field' => 'abstractNote']),
                new Field(['field'  => 'blogTitle', 'baseField' => 'publicationTitle'])
            ]
        ]);

        $this->assertEquals('publicationTitle', $itemType->getField('blogTitle')->baseField);
    }

    public function testSchemaReturnsItemDataType()
    {
        $schema = (new ZoteroSchema([
            'version'   => 1,
            'meta'      => [
                'fields'    => [
                    'date'          => [
                        'type'  => 'date'
                    ],
                    'onLineDate'    => [
                        'type'  => 'date'
                    ]
                ]
            ]
        ]))->addItemTypes([
            [
                'itemType'  => 'journalArticle',
                'fields'    => [
                    ['field'    => 'title'],
                    ['field'    => 'onLineDate']
                ]
            ]
        ], true);

        $itemValueType = $schema->valueType(
            $schema->getItemType('journalArticle')->getField('onLineDate')
        );
        $this->assertEquals('date', $itemValueType);
        $this->assertEquals(
            'text',             // default value type
            $schema->valueType(
                $schema->getItemType('journalArticle')->getField('title')
            )
        );
    }
}
