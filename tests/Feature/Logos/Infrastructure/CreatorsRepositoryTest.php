<?php

declare(strict_types=1);

namespace Tests\Feature\Logos\Infrastructure;

use App\Models\User;
use Tests\TestCase;
use Arete\Logos\Domain\Creator;
use Arete\Logos\Domain\Abstracts\CreatorType;
use Arete\Logos\Application\Ports\Interfaces\CreatorsRepository;

class CreatorsRepositoryTest extends TestCase
{
    public function testCreatesCreatorFromArray(): Creator
    {
        $expectedProperties = [
            'type'          => 'person',
            'attributes'    => [
                'name'      => "Gustavo Raúl",
                'lastName'  => "Ayala"
            ]
        ];
        /** @var CreatorsRepository */
        $creators = $this->app->make(CreatorsRepository::class);
        $creator = $creators->createFromArray($expectedProperties);

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
        $creators = $this->app->make(CreatorsRepository::class);
        $fetchedCreator = $creators->get($storedCreator->id());
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
        /** @var CreatorsRepository */
        $creators = $this->app->make(CreatorsRepository::class);
        $storedCreator->name = "Pedro Raúl";
        $storedCreator->lastName = "Alfonso";
        $creators->save($storedCreator);
        $fetchedCreator = $creators->getNew($storedCreator->id());
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
        /** @var CreatorsRepository */
        $creators = $this->app->make(CreatorsRepository::class);
        $faker = \Faker\Factory::create();
        $randomWord = $faker->word() . ' ' . str_shuffle($faker->word());
        $storedCreator->name .= ' ' . $randomWord;
        $creators->save($storedCreator);
        $creator = $creators->getLike('name', $randomWord)[0];
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
        /** @var CreatorsRepository */
        $creators = $this->app->make(CreatorsRepository::class);

        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $lastName = uniqid('Romero_');
        $alienCreator = $creators->createFromArray([
            'type' => 'person',
            'attributes' => [
                'name'      => "Samuel",
                'lastName'  => $lastName
            ]
        ], $userA->id);

        $notExistentCreator = $creators->getLike(
            'lastName',
            $lastName,
            $userB->id
        );

        $this->assertEquals(0, count($notExistentCreator));
        $existentCreator = $creators->getLike(
            'lastName',
            $lastName,
            $userA->id
        );

        $this->assertGreaterThan(0, count($existentCreator));
        $this->checkCreatoreDataStructure($existentCreator[0], $alienCreator->toArray()['attributes']);

        return $storedCreator;
    }
}
