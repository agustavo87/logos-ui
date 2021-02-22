<?php

namespace Database\Seeders;

use App\Models\Source;
use App\Models\User;
use Illuminate\Database\Seeder;

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

    /**
     * @param \Illuminate\Database\Eloquent\Collection $users
     * 
     * @return void
     */
    public function relateArticles($users): void
    {
        foreach ($users as $i => $user) {
            $sources = $user->sources;
            $creators = $user->creators;
            // Relaciona las fuentes con los creadores
            $sources->each(function ($source) use ($creators) {
                $sourceCreators = $creators->random(rand(1,5));
                $sourceCreators->each(fn ($creator) => $creator->sources()->attach($source->id));
                $source->key = Source::factory()->getKey( strtolower($sourceCreators[0]->data['last_name']), $source->data['year']);
                $source->save();
            });
            
            // Relaciona los artículos con las fuentes
            $user->articles->each(function ($article) use ($sources) {
                $articleSources = $sources->random(rand(2,8));
                $articleSources->each(fn ($artSource) => $artSource->articles()->attach($article->id));

            });
            
        }
    }
}
