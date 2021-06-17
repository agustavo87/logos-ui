<?php

declare(strict_types=1);

namespace Tests\Feature\Logos\Adapters;

use Arete\Logos\Models\Attribute;
use Arete\Logos\Models\ParticipationSet;
use Arete\Logos\Models\SourceInterface;
use Arete\Logos\Models\SourceType;
use Arete\Logos\Models\Source;
use Tests\TestCase;
use Arete\Logos\Ports\Interfaces\SourceRepository;
use Faker\Generator;
use Illuminate\Support\Facades\Log;

class SourceRepositoryTest extends TestCase
{
    protected Generator $faker;

    public function __construct(...$args)
    {
        parent::__construct(...$args);
        $this->faker = \Faker\Factory::create('es_AR');
    }

    public function testSourceRepositoryIsBinded()
    {
        $sources = $this->app->make(SourceRepository::class);
        $this->assertInstanceOf(SourceRepository::class, $sources);
    }

    /**
    * Test de creation of a new source
    *
    * @return Source
    */
    public function testCreatesSourceWithoutCreator(): Source
    {
        $sources = $this->app->make(SourceRepository::class);
        $name = $this->faker->name();
        $sourceData = [
        'type' => 'journalArticle',
        'attributes' => [
            'title' => "El despertar de {$name} a la atención plena.",
            'abstractNote' => "La historia de como {$name} despertó a la atención plena",
            'date' => now(),
            'accessDate' => now(),
            'volume' => 20,
            'issue' => 3
        ]
        // faltaría el usuario al que estaría asociada la fuente
        ];
        $source = $sources->createFromArray($sourceData);
        $this->checkSourceDataStructure($source, $sourceData['attributes']);
        return $source;
    }

    public function checkSourceDataStructure(Source $source, array $expectedAttributes = []): void
    {
        $this->assertInstanceOf(SourceInterface::class, $source);
        $this->assertIsInt($source->id());
        $type = $source->type();
        $this->assertInstanceOf(SourceType::class, $type);
        $this->assertInstanceOf(ParticipationSet::class, $source->participations());
        $arraySource = $source->toArray();
        $this->assertIsArray($arraySource);
        Log::info($arraySource);
        foreach ($expectedAttributes as $code => $value) {
            $attribute = $source->$code;
            $this->assertEquals($value, $attribute);
        }
    }
}


//     /**
//      * @param Source $source
//      *
//      * @depends testCreatesSourceWithoutCreator
//      * @return Source
//      */
//     public function testGetSourceFromRepository(Source $prevSource): Source
//     {
//         $sources = $this->app->make(SourceRepository::class);
//         $newSource = $sources->get($prevSource->id());

//     }
// }
