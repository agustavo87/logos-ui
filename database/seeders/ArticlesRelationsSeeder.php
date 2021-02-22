<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Creator;
use App\Models\Source;
use App\Models\User;
use Illuminate\Database\Seeder;

class ArticlesRelationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();

        $me = $users[0];
        $me->email = 'agustavo87@gmail.com';
        $me->save();
        
        $doe = $users[1];
        $doe->email = 'doe.j@example.com';
        $doe->save();

        // Se asocia cada articulo a un usuario al azar (todos los art. tienen un usuario, no todos los usuarios
        // necesariamente tienen un artículo)
        $articles = Article::all();
        $articles->each(fn ($art) => $art->user()->associate($users->random())->save());

        $sources = Source::all();
        $sources->each(fn ($src) => $src->user()->associate($users->random())->save());
        
        $creators =  Creator::all();
        $creators->each(fn ($crtor) => $crtor->user()->associate($users->random())->save());

        $users->each->refresh();

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
            $sourcesCount = $sources->count();
            $creators = $user->creators;
            $creatorsCount = $creators->count();

            // Relaciona las fuentes con los creadores, si los hay
            if ($creatorsCount && $sourcesCount) {
                $sources->each(function ($source) use ($creators, $creatorsCount) {
                    $i = 0;
                    $sourceCreators = $creators->random(rand(1, $creatorsCount < 5 ? $creatorsCount : 5));
                    foreach ($sourceCreators as $creator) {
                        $creator->sources()->attach(
                            $source->id, [
                                'type' => 'author', 
                                'relevance' => $i++
                            ]
                        );
                    }
                    // $sourceCreators->each(
                    //     function ($creator) use ($i) { $creator->sources()->attach(
                    //         $source->id, [
                    //             'type' => 'author', 
                    //             'relevance' => $creator->sources->count()]
                    //         )
                    //     }
                    // );
                    $source->key = Source::factory()->getKey( strtolower($sourceCreators[0]->data['last_name']), $source->data['year']);
                    $source->save();
                });
            }
            
            // Relaciona los artículos con las fuentes
            if ($sourcesCount) {
                $user->articles->each(function ($article) use ($sources, $sourcesCount) {
                    $articleSources = $sources->random(rand(1,  $sourcesCount < 5 ? $sourcesCount : 5));
                    $articleSources->each(fn ($artSource) => $artSource->articles()->attach($article->id));
                });
            }
            
        }
    }

}
