<?php

declare(strict_types=1);

namespace Tests\Feature\Logos;

use Arete\Logos\Repositories\CreatorTypeRepositoryInterface;
use Arete\Logos\Models\CreatorType;
use Arete\Logos\Models\Attribute;
use Tests\FixturableTestCase;

class CreatorTypeTest extends FixturableTestCase
{

    public function testGetCreatorType()
    {
        $types = $this->app->make(CreatorTypeRepositoryInterface::class);
        $person = $types->get('person');
        $this->assertInstanceOf(CreatorType::class, $person);

        return $person;
    }

    /**
     * @depends testGetCreatorType
     *
     * @param mixed $person
     *
     * @return CreatorType
     */
    public function testHaveExpectedPropertyMethods($person)
    {
        $this->assertEquals('person', $person->code());
        $this->assertEquals('person', (string) $person);
        $this->assertEquals('Person', $person->label());
        $this->assertStringContainsString('l', $person->version());

        return $person;
    }

    /**
     * @depends testHaveExpectedPropertyMethods
     *
     * @param mixed $person
     *
     * @return CreatorType
     */
    public function testHaveExpectedAttributes($person)
    {
        $expectedAttributes = ['name', 'lastName'];
        foreach ($expectedAttributes as $attribute) {
            $this->assertInstanceOf(Attribute::class, $person->$attribute);
        }

        return $person;
    }

    /**
     * @depends testHaveExpectedAttributes
     *
     * @param mixed $person
     *
     * @return CreatorType
     */
    public function testAttributesHaveExpectedStructure($person)
    {
        $name = $person->name;

        $this->assertEquals('name', $name->code);

        $base = $name->base;
        if (!is_null($base)) {
            $this->assertIsString($base);
        }

        $label = $name->label;
        if (!is_null($label)) {
            $this->assertIsString($label);
        }

        $this->assertEquals('text', $name->type);

        $this->assertIsInt($name->order);

        return $person;
    }
}
