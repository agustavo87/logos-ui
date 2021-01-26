<?php

namespace Database\Factories;

use App\Models\Creator;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CreatorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Creator::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $lastName = $this->faker->lastName;
        $name = $this->faker->firstName();

        return [
            'user_id' => User::factory(),
            'key'   => Str::lower($name . Str::substr($lastName, 0, 1)),
            'type' => 'author',
            'schema' => '0.0.1',
            'data' => [
                'name' => $name,
                'last_name' => $lastName
            ]
        ];
    }
}
