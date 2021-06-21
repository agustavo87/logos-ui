<?php

declare(strict_types=1);

namespace Tests\Feature\Logos\Infrastructure;

use Arete\Logos\Application\Ports\Interfaces\CreatorTypeRepository;
use Arete\Logos\Domain\Abstracts\CreatorType;
use Arete\Logos\Domain\Attribute;
use Tests\TestCase;

class CreatorTypeRepositoryTest extends TestCase
{

    public function testGetPersonCreatorType()
    {
        $types = $this->app->make(CreatorTypeRepository::class);
        $person = $types->get('person');
        $this->assertInstanceOf(CreatorType::class, $person);

        return $person;
    }

    /**
     * @depends testGetPersonCreatorType
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
    public function testPersonHaveExpectedAttributes($person)
    {
        $expectedAttributes = ['name', 'lastName'];
        foreach ($expectedAttributes as $attribute) {
            $this->assertInstanceOf(Attribute::class, $person->$attribute);
        }

        return $person;
    }

    /**
     * @depends testPersonHaveExpectedAttributes
     *
     * @param mixed $person
     *
     * @return CreatorType
     */
    public function testPersonAttributesHaveExpectedStructure($person)
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
