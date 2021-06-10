<?php

declare(strict_types=1);

namespace Arete\Logos\Tests;

use PHPUnit\Framework\TestCase;
use Arete\Logos\Models\ParticipationInterface;
use Arete\Logos\Factories\Participation;
use Arete\Logos\Models\CreatorType;
use Arete\Logos\Models\Role;

class ParticipationTest extends TestCase
{
    /**
    * Construction of Participation
    *
    * @return void
    */
    public function testParticipationConstruction()
    {
        $sbCreatorType = $this->createStub(CreatorType::class);
        $sbRole = $this->createStub(Role::class);
        $participation = Participation::fromArray([
            'attributes'    => [
                'names'         => 'Gustavo Raúl',
                'lastNames'      => 'Ayala'
            ],
            'creatorId'     => 21,
            'relevance'     => 2,
            'creatorType'   => $sbCreatorType,
            'role'          => $sbRole,
        ]);

        $this->assertInstanceOf(ParticipationInterface::class, $participation);

        $this->assertEquals('Gustavo Raúl', $participation->names);
        $this->assertEquals('Ayala',        $participation->lastNames);
        $this->assertEquals(2,              $participation->relevance());
        $this->assertEquals(21,             $participation->creatorId());
        $this->assertEquals($sbCreatorType, $participation->creatorType());
        $this->assertEquals($sbRole,        $participation->role());
    }
}
