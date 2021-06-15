<?php

declare(strict_types=1);

namespace Tests\Feature\Logos\Adapters;

use Arete\Logos\Models\ParticipationSet;
use Arete\Logos\Models\SourceInterface;
use Arete\Logos\Models\SourceType;
use Tests\TestCase;
use Arete\Logos\Ports\Interfaces\SourceRepository;

class SourceRepositoryTest extends TestCase
{

    public function testSourceRepositoryIsBinded()
    {
        $sources = $this->app->make(SourceRepository::class);
        $this->assertInstanceOf(SourceRepository::class, $sources);
    }

    /**
    * Test de creation of a new source
    *
    * @return void
    */
    public function testCreatesSourceWithoutCreator()
    {
        $sources = $this->app->make(SourceRepository::class);
        $source = $sources->createFromArray([
            'type' => 'journalArticle',
            'attributes' => [
                'title' => 'El despertar de Goneka a la atención plena.',
                'abstractNote' => 'La historia de como Goneka despertó a la atención plena',
                'date' => now(),
                'accessDate' => now(),
                'volume' => 20,
                'issue' => 3
            ]
            // faltaría el usuario al que estaría asociada la fuente
        ]);
        $this->assertInstanceOf(SourceInterface::class, $source);
        $this->assertIsInt($source->id());
        $this->assertInstanceOf(SourceType::class, $source->type());
        $this->assertInstanceOf(ParticipationSet::class, $source->participations());
    }
}
