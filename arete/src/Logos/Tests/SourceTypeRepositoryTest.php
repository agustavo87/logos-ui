<?php

namespace Arete\Logos\Tests;

use Arete\Logos\Application\LogosContainer;
use PHPUnit\Framework\TestCase;
use Arete\Logos\Domain\Abstracts\RoleCollection;
use Arete\Logos\Domain\Role;
use Arete\Logos\Domain\Abstracts\SourceType;
use Arete\Logos\Domain\Attribute;
use Arete\Logos\Application\TestSourcesProvider;
use Arete\Logos\Application\Ports\Interfaces\SourceTypeRepository;

class SourceTypeRepositoryTest extends TestCase
{

    public static function setUpBeforeClass(): void
    {
        // boot container
        // Loads ZoteroSourceTypeRepository by default and other basic/testing adapters.
        LogosContainer::pushProvider(TestSourcesProvider::class);
        LogosContainer::load();
    }

    public function testTypeRepositoryTestIsBinded(): SourceTypeRepository
    {
        $sourceTypes = LogosContainer::get(SourceTypeRepository::class);
        $this->assertInstanceOf(SourceTypeRepository::class, $sourceTypes);
        return $sourceTypes;
    }

    /**
     * @param SourceTypeRepository $sourceTypes
     *
     * @depends testTypeRepositoryTestIsBinded
     * @return SourceType
     */
    public function testGetZoteroBasicJournalArticleType(SourceTypeRepository $sourceTypes): SourceType
    {
        $type = $sourceTypes->get('journalArticle');
        $this->assertInstanceOf(SourceType::class, $type);
        $this->assertEquals('journalArticle', $type->code());
        $this->assertEquals('journalArticle', (string) $type);
        $this->assertEquals('Journal Article', $type->label());
        $this->assertStringContainsString('z', $type->version());

        $validFields = [
            'title', 'abstractNote', 'publicationTitle', 'volume', 'issue',
            'pages', 'date', 'series', 'seriesTitle', 'seriesText',
            'journalAbbreviation', 'language', 'DOI', 'ISSN', 'shortTitle',
            'url', 'accessDate', 'archive', 'archiveLocation',
            'libraryCatalog', 'callNumber', 'rights', 'extra'
        ];
        foreach ($validFields as $field) {
            $this->assertInstanceOf(Attribute::class, $type->$field);
        }

        return $type;
    }

    /**
     * @depends testGetZoteroBasicJournalArticleType
     *
     * @param SourceType $type
     *
     * @return SourceType
     */
    public function testJournalArticleTypeHasRoleCollection(SourceType $type): SourceType
    {
        $roles = $type->roles();
        $this->assertInstanceOf(RoleCollection::class, $roles);
        $this->assertEquals($type, $roles->type());

        $validRoles = ['author', 'contributor', 'editor', 'translator', 'reviewedAuthor'];
        foreach ($validRoles as $role) {
            $this->assertInstanceOf(Role::class, $roles->$role);
        }

        return $type;
    }

    /**
     * @depends testJournalArticleTypeHasRoleCollection
     *
     * @param SourceType $type
     *
     * @return SourceType
     */
    public function testJournalArticleAttributeHasExpectedStructure(SourceType $type): SourceType
    {
        /**  @var Attribute */
        $titleAttribute = $type->title;
        $this->assertEquals('title', $titleAttribute->code);
        $this->assertEquals('text', $titleAttribute->type);
        $label = $titleAttribute->label;
        if (!is_null($label)) {
            $this->assertIsString($label);
        }
        $this->assertIsInt($titleAttribute->order);

        return $type;
    }

    /**
     * @depends testJournalArticleAttributeHasExpectedStructure
     *
     * @param SourceType $type
     *
     * @return SourceType
     */
    public function testJournalArticleRoleHasExpectedStructure(SourceType $type): SourceType
    {
        /** @var Role */
        $authorRole = $type->roles()->author;

        $this->assertEquals('author', $authorRole->code);
        $label = $authorRole->label;
        if (!is_null($label)) {
            $this->assertIsString($label);
        }
        $this->assertIsBool($authorRole->primary);
        return $type;
    }

    public function testGetsAvailableTypes()
    {
        /** @var SourceTypeRepository */
        $types = LogosContainer::get(SourceTypeRepository::class);
        $availableTypes = $types->types();
        // basic types in simple schema loader of zotero.
        $this->assertContains('journalArticle', $availableTypes);
        $this->assertContains('book', $availableTypes);
    }
}
