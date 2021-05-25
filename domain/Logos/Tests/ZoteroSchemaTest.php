<?php

declare(strict_types=1);

namespace Arete\Logos\Tests;

use Arete\Logos\Models\Zotero\{CreatorType, CSLMap, Field, ItemType, Schema};
use Arete\Logos\Tests\Traits\ChecksZoteroSchemaDataStructure;
use PHPUnit\Framework\TestCase;

class ZoteroSchemaTest extends TestCase
{
    use ChecksZoteroSchemaDataStructure;

    public function testSchemaCreatesWithDefaults()
    {
        $schema = new Schema();
        $this->checkSchemaDataStructure($schema);
    }

    public function testSchemaMergesDefaults()
    {
        $schema = new Schema([
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
}
