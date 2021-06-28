<?php

namespace Database\Seeders;

use App\Models\User;
use Arete\Logos\Application\Ports\Interfaces\CreatorsRepository;
use Arete\Logos\Application\Ports\Interfaces\SourcesRepository;
use DateTime;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SourceSeeder extends Seeder
{
    protected static int $sourcesN = 5;
    protected static int $creatorsN = 3;

    protected Generator $faker;

    protected SourcesRepository $sources;

    protected CreatorsRepository $creators;

    public function __construct(
        FakerFactory $fakerFactory,
        SourcesRepository $sources,
        CreatorsRepository $creators
    ) {
        $this->faker = $fakerFactory::create();
        $this->sources = $sources;
        $this->creators = $creators;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ownersID = User::all('id');
        $creatorsCollection = collect($this->createCreators(self::$creatorsN));
        for ($i = 1; $i <= self::$sourcesN; $i++) {
            $ownerID = $ownersID->random();
            $pageInit =  $this->faker->numberBetween(7, 488);
            $pageEnd = $pageInit + $this->faker->numberBetween(1, 20);
            $this->sources->createFromArray([
                'type' => 'journalArticle',
                'attributes' => [
                    'title' => substr(Str::title($this->faker->sentence), 0, -1), // substr quita el punto final
                    'abstractNote' =>  substr($this->faker->paragraph(8), 0, -1) ,
                    'date' => DateTime::createFromFormat('Y', $this->faker->numberBetween(1980, 2021)),
                    'publicationTitle' => substr(Str::title($this->faker->sentence), 0, -1),
                    'volume' => $this->faker->numberBetween(1, 60),
                    'issue' => $this->faker->numberBetween(1, 4),
                    'pages' =>  $pageInit . '-' . $pageEnd
                ],
                'participations' => [
                    [
                        'role' => 'author',
                        'relevance' => 1,
                        'creator' => [
                            'creatorID' => $creatorsCollection->random()->id()
                        ]
                    ]
                ]
            ], $ownerID);
        }
    }

    public function createCreators(int $n): array
    {
        $creatorsCollection = [];
        for ($i = 1; $i <= $n; $i++) {
            $creatorsCollection[] = $this->creators->createFromArray([
                'type'          => 'person',
                'attributes'    => [
                    'name'      => $this->faker->firstName() . ' ' . $this->faker->firstName(),
                    'lastName'  => $this->faker->lastName
                ]
            ]);
        }
        return $creatorsCollection;
    }
}
