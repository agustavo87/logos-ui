<?php

namespace Database\Factories;

use App\Models\Source;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SourceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Source::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        if(rand(0,1)) {
            return $this->journalArticle();
        } else {
            return $this->book();
        }
    }

    public function journalArticle()
    {
        $year = $this->faker->numberBetween(1980, 2021);

        $name = $this->faker->firstName;
        $key = $this->getKey($name, $year);
        
        $title = Str::title($this->faker->sentence);
        $journal = Str::title($this->faker->sentence);
        $volume = $this->faker->numberBetween(1,60);
        $issue = $this->faker->numberBetween(1,4);
        $pageInit = $this->faker->numberBetween(7,488);
        $length = $this->faker->numberBetween(0,20);
        

        return [
            'user_id' => User::factory(),
            'key' => $key,
            'type' => 'citation.article',
            'schema' => '0.0.1',
            'data' => [
                'year' => $year,
                'title' => $title,
                'journal' => $journal,
                'volume' => $volume,
                'issue' => $issue,
                'firstPage' => $pageInit,
                'lastPage' => $pageInit + $length
            ]
        ];
    }

    public function book()
    {
        $year = $this->faker->numberBetween(1930, 2021);
        $name = $this->faker->firstName;
        $key = $this->getKey($name, $year);
        
        $title = Str::title($this->faker->sentence);
        $editorial = Str::title($this->faker->word);
        $city = Str::title($this->faker->city);

        return [
            'user_id' => User::factory(),
            'key' => $key,
            'type' => 'citation.book',
            'schema' => '0.0.1',
            'data' => [
                'year' => $year,
                'title' => $title,
                'editorial' => $editorial,
                'city' => $city
            ]
        ];
    }

    public function getKey ($name, $year)
    {
        $abc = "abcdefghijkmnopqrstuvwxyz0123456789"; // soporta solo unas cuantas repeticiones.
        $base = "{$name}{$year}";
        $try = $base;
        $i = 0;
       while (DB::table('sources')->where('key', $try)->exists()) {
           $try = $base . $abc[$i++];
       }
       return $try;
    }
}
