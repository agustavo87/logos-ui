<?php

namespace Tests\Feature\Logos\Infrastructure;

use Tests\TestCase;
use Arete\Logos\Domain\Abstracts\RoleCollection;
use Arete\Logos\Domain\Role;
use Arete\Logos\Domain\Abstracts\SourceType;
use Arete\Logos\Domain\Attribute;
use Arete\Logos\Application\Ports\Interfaces\SourceTypeRepository;

class SourceTypeRepositoryTest extends TestCase
{
    /**
     * @return SourceType
     */
    public function test_get_zotero_basic_journal_article_type(): SourceType
    {
        $types = $this->app->make(SourceTypeRepository::class);
        $type = $types->get('journalArticle');
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
     * @depends test_get_zotero_basic_journal_article_type
     *
     * @param SourceType $roles
     *
     * @return SourceType
     */
    public function test_journal_article_type_has_role_collection(SourceType $type): SourceType
    {
        $roles = $type->participations();
        $this->assertInstanceOf(RoleCollection::class, $roles);
        $this->assertEquals($type, $roles->type());

        $validRoles = ['author', 'contributor', 'editor', 'translator', 'reviewedAuthor'];
        foreach ($validRoles as $role) {
            $this->assertInstanceOf(Role::class, $roles->$role);
        }

        return $type;
    }

    /**
     * @depends test_journal_article_type_has_role_collection
     *
     * @param SourceType $type
     *
     * @return SourceType
     */
    public function test_journal_article_attribute_has_expected_structure(SourceType $type): SourceType
    {
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
     * @depends test_journal_article_attribute_has_expected_structure
     *
     * @param SourceType $type
     *
     * @return SourceType
     */
    public function test_journal_article_role_has_expected_structure(SourceType $type): SourceType
    {
        $authorRole = $type->participations()->author;

        $this->assertEquals('author', $authorRole->code);
        $label = $authorRole->label;
        if (!is_null($label)) {
            $this->assertIsString($label);
        }
        $this->assertIsBool($authorRole->primary);
        return $type;
    }
}
