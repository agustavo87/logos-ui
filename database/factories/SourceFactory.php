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
        $lastName = $this->faker->lastName;
        $year = $this->faker->unique()->numberBetween(1970, 2021);
        
        $nameInitial = Str::upper($this->faker->randomLetter);
        $title = Str::title($this->faker->sentence);
        $editorial = Str::title($this->faker->word);
        $city = Str::title($this->faker->city);

        return [
            'user_id' => User::factory(),
            'key' => "$lastName$year",
            'data' => "$lastName, $nameInitial. ($year). $title $editorial: $city."
        ];
    }
}
