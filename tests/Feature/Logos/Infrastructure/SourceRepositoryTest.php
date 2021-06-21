<?php

declare(strict_types=1);

namespace Tests\Feature\Logos\Infrastructure;

use Arete\Logos\Domain\ParticipationSet;
use Arete\Logos\Domain\Abstracts\SourceType;
use Arete\Logos\Domain\Source;
use Tests\TestCase;
use Arete\Logos\Ports\Interfaces\SourceRepository;
use Faker\Generator;

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
            'volume' => $this->faker->numberBetween(1, 50),
            'issue' => $this->faker->numberBetween(1, 4)
        ]
        // faltaría el usuario al que estaría asociada la fuente
        ];
        $source = $sources->createFromArray($sourceData);
        $this->checkSourceDataStructure($source, $sourceData['attributes']);
        return $source;
    }

    /**
     * Test if get a source correctly
     *
     * @param Source $source
     * @depends testCreatesSourceWithoutCreator
     * @return Source
     */
    public function testGetSource(Source $storedSource): Source
    {
        $sources = $this->app->make(SourceRepository::class);
        $fetchedSource = $sources->get($storedSource->id());
        $this->checkSourceDataStructure($fetchedSource, $storedSource->toArray());
        return $storedSource;
    }

    public function checkSourceDataStructure(Source $source, array $expectedAttributes = []): void
    {
        $this->assertInstanceOf(Source::class, $source);
        $this->assertIsInt($source->id());
        $type = $source->type();
        $this->assertInstanceOf(SourceType::class, $type);
        $this->assertInstanceOf(ParticipationSet::class, $source->participations());
        $arraySource = $source->toArray();
        $this->assertIsArray($arraySource);
        // Log::info($arraySource);
        foreach ($expectedAttributes as $code => $value) {
            $attribute = $source->$code;
            $this->assertEquals($value, $attribute);
        }
    }
}
