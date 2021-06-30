<?php

declare(strict_types=1);

namespace Arete\Logos\Tests;

use Arete\Logos\Application\LogosContainer;
use Arete\Logos\Application\Ports\Interfaces\CreatorTypeRepository;
use Arete\Logos\Application\Ports\Interfaces\ParticipationRepository;
use Arete\Logos\Application\Ports\Interfaces\SourceTypeRepository;
use Arete\Logos\Application\TestSourcesProvider;
use Arete\Logos\Domain\Contracts\Participation;
use Arete\Logos\Domain\Creator;
use Arete\Logos\Domain\SimpleFormatter;
use Arete\Logos\Domain\Source;
use PHPUnit\Framework\TestCase;

class ParticipationRepositoryTest extends TestCase
{
    protected SourceTypeRepository $sourceTypes;
    protected CreatorTypeRepository $creatorTypes;
    protected ParticipationRepository $participations;

    public static function setUpBeforeClass(): void
    {
        // boot container
        // Loads ZoteroSourceTypeRepository by default and other basic/testing adapters.
        LogosContainer::pushProvider(TestSourcesProvider::class);
        LogosContainer::load();
    }

    public function setUp(): void
    {
        $this->sourceTypes = LogosContainer::get(SourceTypeRepository::class);
        $this->creatorTypes = LogosContainer::get(CreatorTypeRepository::class);
        $this->participations = LogosContainer::get(ParticipationRepository::class);
    }

    public function testParticipationRepositoryIsBinded(): ParticipationRepository
    {
        $participations = LogosContainer::get(ParticipationRepository::class);
        $this->assertInstanceOf(ParticipationRepository::class, $participations);
        return $participations;
    }

    /**
     * @param ParticipationRepository $participations
     *
     * @depends testParticipationRepositoryIsBinded
     * @return void
     */
    public function testCreateParticipation(ParticipationRepository $participations): array
    {
        $ownerID = 3;
        $source = $this->getSource([
            'id' => 5,
            'typeCode' => 'journalArticle',
            'ownerID' => $ownerID
        ]);
        $creator = $this->getCreator([
            'id' => 3,
            'typeCode' => 'person',
            'ownerID ' => $ownerID
        ]);

        $participation = $participations->create($source, $creator, 'author', 2);
        $this->assertInstanceOf(Participation::class, $participation);

        return [$source, $creator];
    }

    /**
     * @param array $legacy
     *
     * @depends testCreateParticipation
     * @return array
     */
    public function testLoadsParticipation(array $legacy): array
    {
        list($source, $creator) = $legacy;
        $this->participations->create($source, $creator, 'author', 2);
        /** @var Participation */
        $origParticipation = $this->participations->load($source)[0];
        $this->assertSame($source, $origParticipation->source());
        return [$source, $creator];
    }

    /**
     * @param array $legacy
     *
     * @depends testLoadsParticipation
     * @return void
     */
    public function testSavesParticipation(array $legacy)
    {
        list($source, $creator) = $legacy;
        $this->participations->create($source, $creator, 'author', 2);
        $participation = $this->participations->load($source)[0];
        $participation->setRelevance(20);
        $this->participations->save($participation);

        $sameParticipation = $this->participations->load($source)[0];
        $this->assertEquals(20, $sameParticipation->relevance());
    }

    public function getSource(array $properties): Source
    {
        return new Source(
            $this->sourceTypes,
            new SimpleFormatter(),
            $properties
        );
    }

    public function getCreator(array $properties = [], array $attributes = []): Creator
    {
        return new Creator(
            $this->creatorTypes,
            $properties,
            $attributes
        );
    }
}
