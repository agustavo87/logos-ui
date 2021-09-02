<?php

declare(strict_types=1);

namespace Arete\Logos\Tests;

use Arete\Logos\Application\LogosContainer;
use PHPUnit\Framework\TestCase;
use Arete\Logos\Domain\Creator;
use Arete\Logos\Domain\Abstracts\CreatorType;
use Arete\Logos\Application\Ports\Interfaces\CreatorsRepository;
use Arete\Logos\Application\TestSourcesProvider;

class CreatorsRepositoryTest extends TestCase
{
    public static $testUser = [
        'A' => 2,
        'B' => 5
    ];
    public static CreatorsRepository $creators;
    public static function setUpBeforeClass(): void
    {
        // boot container
        // Loads CreatorTypeRepository by default and other basic/testing adapters.
        LogosContainer::pushProvider(TestSourcesProvider::class);
        LogosContainer::load();
        self::$creators = LogosContainer::get(CreatorsRepository::class);
    }

    public function testCreatorTypeRepositoryTestIsBinded()
    {
        $this->assertInstanceOf(CreatorsRepository::class, self::$creators);
    }

    public function testCreatesCreatorFromArray(): Creator
    {
        $expectedProperties = [
            'type'          => 'person',
            'attributes'    => [
                'name'      => "Gustavo Raúl",
                'lastName'  => "Ayala"
            ]
        ];
        $creator = self::$creators->createFromArray($expectedProperties, self::$testUser['A']);

        $this->checkCreatoreDataStructure($creator, $expectedProperties['attributes']);
        return $creator;
    }

    public function checkCreatoreDataStructure(Creator $creator, array $expectedAttributes = []): void
    {
        $this->assertInstanceOf(Creator::class, $creator);
        $this->assertIsInt($creator->id());
        $type = $creator->type();
        $this->assertInstanceOf(CreatorType::class, $type);

        $arrayCreator = $creator->toArray();
        $this->assertIsArray($arrayCreator);
        foreach ($expectedAttributes as $code => $expectedValue) {
            $actualValue = $creator->$code;
            $this->assertEquals($expectedValue, $actualValue);
        }
    }

    /**
     * @param Creator $storedCreator
     *
     * @depends testCreatesCreatorFromArray
     * @return Creator
     */
    public function testGetCreator(Creator $storedCreator): Creator
    {
        $fetchedCreator = self::$creators->get($storedCreator->id());
        $this->checkCreatoreDataStructure($fetchedCreator, $storedCreator->toArray()['attributes']);
        return $storedCreator;
    }

    /**
     * @param Creator $storedCreator
     *
     * @depends testGetCreator
     * @return Creator
     */
    public function testSaveCreator(Creator $storedCreator): Creator
    {
        $storedCreator->name = "Pedro Raúl";
        $storedCreator->lastName = "Alfonso";
        self::$creators->save($storedCreator);
        $fetchedCreator = self::$creators->getNew($storedCreator->id());
        $this->assertEquals("Pedro Raúl", $fetchedCreator->name);
        $this->assertEquals("Alfonso", $fetchedCreator->lastName);
        return $fetchedCreator;
    }

    /**
     * @depends testSaveCreator
     * @return Creator
     */
    public function testGetLikeCreator(Creator $storedCreator): Creator
    {
        $faker = \Faker\Factory::create();
        $randomWord = $faker->word() . ' ' . str_shuffle($faker->word());
        $storedCreator->name .= ' ' . $randomWord;
        self::$creators->save($storedCreator);
        $creator = self::$creators->getLike('name', $randomWord)[0];
        $this->assertStringContainsString($randomWord, $creator->name);
        $this->assertEquals($storedCreator->lastName, $creator->lastName);
        return $creator;
    }

    /**
     * @param Creator $storedCreator
     *
     * @depends testGetLikeCreator
     * @return Creator
     */
    public function testGetCreatorByOwner(Creator $storedCreator): Creator
    {
        $lastName = uniqid('Romero_');
        $alienCreator = self::$creators->createFromArray([
            'type' => 'person',
            'attributes' => [
                'name'      => "Samuel",
                'lastName'  => $lastName
            ]
        ], self::$testUser['B']);

        $notExistentCreator = self::$creators->getLike(
            'lastName',
            $lastName,
            self::$testUser['A']
        );
        $this->assertEquals(0, count($notExistentCreator));
        $existentCreator = self::$creators->getLike(
            'lastName',
            $lastName,
            self::$testUser['B']
        );
        $this->assertGreaterThan(0, count($existentCreator));
        $this->checkCreatoreDataStructure($existentCreator[0], $alienCreator->toArray()['attributes']);

        return $storedCreator;
    }
}
