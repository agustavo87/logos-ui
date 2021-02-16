<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Creator;
use App\Models\Source;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $users = User::factory()
            ->hasArticles(12)
            ->hasSources(19)
            ->hasCreators(30)
            ->count(7)
            ->create();

        $me = $users->first();
        $me->email = 'agustavo87@gmail.com';
        $me->save();

        $doe = $users[1];
        $doe->email = 'doe.j@example.com';
        $doe->save();

        $this->relateArticles($users);
    }

    public function relateArticles($users)
    {
        foreach ($users as $i => $user) {

            // Relaciona las fuentes con los creadores
            $user->sources->each(function ($source, $key) use ($user) {
                $usedCreators = [];
                for ($i=0; $i < 2; $i++) { 
                    $availableCreators = $user->creators->diff($usedCreators);
                    $pickedCreator = $availableCreators->random();
                    $usedCreators[] = $pickedCreator;
                    $source->creators()->attach($pickedCreator);
                }
                // $source->key = Str::lower($usedCreators[0]->data['last_name']) . $source->data['year'];
                $source->key = Source::factory()->getKey(Str::lower($usedCreators[0]->data['last_name']), $source->data['year']);
                $source->save();
            });
            
            // Relaciona los artículos con las fuentes
            $user->articles->each(function ($article, $key) use ($user) {
                $usedSources = [];
                for ($i=0; $i < 3; $i++) { 
                    $availableSources = $user->sources->diff($usedSources);
                    $pickedSource = $availableSources->random();
                    $usedSources[] = $pickedSource;
                    $article->sources()->attach($pickedSource);
                }
            });
            
        }
    }
}
