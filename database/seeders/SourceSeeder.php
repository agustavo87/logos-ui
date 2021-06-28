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
    protected static int $sourcesPerUser = 10;
    protected static int $creatorsPerUser = 4;

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
        $ownersID = User::all('id')->pluck('id');
        $creatorsCollection = $this->createUserCreators($ownersID->toArray(), self::$creatorsPerUser);
        foreach ($ownersID as $ID) {
            $this->createUserSources(self::$sourcesPerUser, $ID, $creatorsCollection[$ID]);
        }
    }

    /**
     * @param int $n
     * @param int $ownerID
     * @param \Illuminate\Support\Collection $creators
     *
     * @return void
     */
    public function createUserSources(int $n, int $ownerID, $creators)
    {
        for ($i = 1; $i <= $n; $i++) {
            $ownerID = $ownerID;
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
                            'creatorID' => $creators->random()->id()
                        ]
                    ]
                ]
            ], $ownerID);
        }
    }

    /**
     * @param int $n
     *
     * @return \Arete\Logos\Domain\Creator[]
     */
    public function createCreators($ownerID, int $n): array
    {
        $creatorsCollection = [];
        for ($i = 1; $i <= $n; $i++) {
            $creatorsCollection[] = $this->creators->createFromArray([
                'type'          => 'person',
                'attributes'    => [
                    'name'      => $this->faker->firstName() . ' ' . $this->faker->firstName(),
                    'lastName'  => $this->faker->lastName
                ]
            ], $ownerID);
        }
        return $creatorsCollection;
    }

    /**
     * @param int[] $usersIDs
     * @param int $creatorsPerUser
     *
     * @return array
     */
    public function createUserCreators(array $usersIDs, int $creatorsPerUser): array
    {
        $usersCreators = [];
        foreach ($usersIDs as $ownerID) {
            $usersCreators[$ownerID] = collect($this->createCreators($ownerID, $creatorsPerUser));
        }
        return $usersCreators;
    }
}
