<?php

declare(strict_types=1);

namespace Arete\Logos\Tests;

use PHPUnit\Framework\TestCase;
use Arete\Logos\Models\ParticipationSet;
use Arete\Logos\Models\ParticipationInterface;
use Arete\Logos\Models\SourceInterface;

class ParticipationSetTest extends TestCase
{
    /**
    * Construction of Participation Set
    *
    * @return void
    */
    public function testParticipationSetConstruction()
    {
        $expParticipations = [ // expected particpations
            'author' => [
                12 => $this->createParticipationStub(12),
                13 => $this->createParticipationStub(13),
                14 => $this->createParticipationStub(14),
            ],
            'editor' => [
                17 => $this->createParticipationStub(17),
                18 => $this->createParticipationStub(18),
            ],
            'translator' => [
                19 => $this->createParticipationStub(19)
            ]
        ];
        $sbSource = $this->createStub(SourceInterface::class);  // Source Stub
        $participationSet = new ParticipationSet($sbSource);

        foreach ($expParticipations as $role => $participations) {
            foreach ($participations as $creatorId => $participation) {
                $participationSet->push($role, $participation);
            }
        }

        foreach ($participationSet->roles() as $role) {
            foreach ($participationSet->$role as $creatorId => $participation) {
                $this->assertSame($expParticipations[$role][$creatorId], $participation);
            }
        }

        $this->assertSame($sbSource, $participationSet->source());
    }

    public function createParticipationStub($id)
    {
        $stub = $this->createStub(ParticipationInterface::class);
        $stub->method('creatorId')
             ->willReturn($id);
        return $stub;
    }
}
