<?php

declare(strict_types=1);

namespace Tests\Feature\Logos\Infrastructure;

use Arete\Logos\Domain\Creator;
use Arete\Logos\Domain\Abstracts\CreatorType;
use Tests\TestCase;
use Arete\Logos\Ports\Interfaces\CreatorsRepository;

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
        $this->checkCreatoreDataStructure($fetchedCreator, $storedCreator->toArray());
        return $storedCreator;
    }
}
