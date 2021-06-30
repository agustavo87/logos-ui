<?php

declare(strict_types=1);

namespace Arete\Logos\Tests;

use PHPUnit\Framework\TestCase;
use Arete\Logos\Application\LogosContainer;
use Arete\Logos\Application\Ports\Interfaces\CreatorTypeRepository;
use Arete\Logos\Application\TestSourcesProvider;
use Arete\Logos\Domain\Abstracts\CreatorType;
use Arete\Logos\Domain\Attribute;

class CreatorTypeRepositoryTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        // boot container
        // Loads CreatorTypeRepository by default and other basic/testing adapters.
        LogosContainer::pushProvider(TestSourcesProvider::class);
        LogosContainer::load();
    }

    public function testCreatorTypeRepositoryTestIsBinded(): CreatorTypeRepository
    {
        $sourceTypes = LogosContainer::get(CreatorTypeRepository::class);
        $this->assertInstanceOf(CreatorTypeRepository::class, $sourceTypes);
        return $sourceTypes;
    }

    /**
     * @param CreatorTypeRepository $types
     *
     * @depends testCreatorTypeRepositoryTestIsBinded
     * @return CreatorType
     */
    public function testGetPersonCreatorType(CreatorTypeRepository $types): CreatorType
    {
        $person = $types->get('person');
        $this->assertInstanceOf(CreatorType::class, $person);

        return $person;
    }

    /**
     * @param CreatorType $person
     *
     * @depends testGetPersonCreatorType
     * @return CreatorType
     */
    public function testHaveExpectedPropertyMethods(CreatorType $person): CreatorType
    {
        $this->assertEquals('person', $person->code());
        $this->assertEquals('person', (string) $person);
        $this->assertEquals('Person', $person->label());
        $this->assertStringContainsString('l', $person->version());

        return $person;
    }

    /**
     * @param CreatorType $person
     *
     * @depends testHaveExpectedPropertyMethods
     * @return CreatorType
     */
    public function testPersonHaveExpectedAttributes(CreatorType $person): CreatorType
    {
        $expectedAttributes = ['name', 'lastName'];
        foreach ($expectedAttributes as $attribute) {
            $this->assertInstanceOf(Attribute::class, $person->$attribute);
        }

        return $person;
    }

    /**
     * @param CreatorType $person
     *
     * @depends testPersonHaveExpectedAttributes
     * @return CreatorType
     */
    public function testPersonAttributesHaveExpectedStructure(CreatorType $person): CreatorType
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
