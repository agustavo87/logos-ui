<?php

namespace Tests\Feature\Logos;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\FixturableTestCase;
use Arete\Logos\Models\RoleCollection;
use Arete\Logos\Models\Role;
use Arete\Logos\Models\SourceType;
use Arete\Logos\Models\Attribute;

class TypeTest extends FixturableTestCase
{
    /**
     * @return SourceType
     */
    public function test_get_zotero_basic_jorunal_article_type(): SourceType
    {
        $types = app('\Arete\Logos\Repository\TypeRepositoryInterface');
        $type = $type->get('journalArticle');
        $this->assertInstanceOf(SourceType::class, $type);
        $this->assertEquals('journalArticle', $type->code());
        $this->assertEquals('journalArticle', (string) $type);
        $this->assertEquals('Journal Article', $type->label());
        $this->assertEquals('1.0', $type->version());

        $validFields = [
            'title', 'abstractNote', 'publicationTitle', 'volume', 'issue',
            'pages', 'date', 'series', 'seriesTitle', 'seriesText',
            'journalAbbreviation', 'language', 'DOI', 'ISSN', 'shortTitle',
            'url', 'accessDate', 'archive', 'archiveLocation',
            'libraryCatalog', 'callNumber', 'rights', 'extra'
        ];
        foreach ($validFields as $field) {
            $this->assertObjectHasAttribute($field, $type);
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
        $roles = $type->roles();
        $this->assertInstanceOf(RoleCollection::class, $roles);
        $this->assertEquals($type, $roles->type());

        $validRoles = ['author', 'contributor', 'editor', 'translator', 'reviewedAuthor'];
        foreach ($validRoles as $role) {
            $this->assertObjectHasAttribute($role, $roles);
            $this->assertInstanceOf(Role::class, $roles->$role);
        }

        return $type;
    }

    /**
     * @depends test_journal_article_has_role_collection
     *
     * @param SourceType $type
     *
     * @return SourceType
     */
    public function test_journal_article_attribute_has_expected_structure(SourceType $type): SourceType
    {
        $titleAttribute = $type->title;
        $this->assertEquals('title', $titleAttribute->code);
        $this->assertEquals('', $titleAttribute->base);
        $this->assertEquals('text', $titleAttribute->type);
        $this->assertIsString($titleAttribute->label);
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
        $authorRole = $type->roles()->author;

        $this->assertEquals('author', $authorRole->code);
        $this->assertIsString($authorRole->label);
        $this->assertIsBool($authorRole->primary);
        return $type;
    }
}
