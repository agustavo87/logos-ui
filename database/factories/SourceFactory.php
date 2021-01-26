<?php

namespace Database\Factories;

use App\Models\Source;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
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
        $year = $this->faker->unique()->numberBetween(1970, 2021);
        
        // $lastName = $this->faker->lastName; // by the creators relationship
        // $name = $this->faker->firstName();
        $title = Str::title($this->faker->sentence);
        $editorial = Str::title($this->faker->word);
        $city = Str::title($this->faker->city);

        return [
            'user_id' => User::factory(),
            'key' => "doe$year",
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
}
