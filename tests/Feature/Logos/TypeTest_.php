<?php

namespace Tests\Feature\Logos;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\FixturableTestCase;

class TypeTest extends FixturableTestCase
{

    public function test_get_zotero_basic_jorunal_type()
    {
        $types = app('\Arete\Logos\Repository\TypeRepositoryInterface');
        $type = $type->get('journalArticle');
        $this->assertEquals('journalArticle', $type->code());
        $this->assertEquals('journalArticle', (string) $type);
        $this->assertEquals('Journal Article', $type->label());
        $this->assertEquals('1.0', $type->version());

        $validFields = ['title', 'abstractNote'];
        foreach ($type->names as $code) {
            $attr = $type->$code;
            $this->assertContains($attr->code, $validFields);
        }
    }
}
