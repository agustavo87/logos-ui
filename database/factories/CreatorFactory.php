<?php

namespace Database\Factories;

use App\Models\Creator;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

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
            'key'   => $this->getKey($name, $lastName),
            // 'key'   => Str::lower($lastName . Str::substr($name, 0, 1)),
            'type' => 'author',
            'schema' => '0.0.1',
            'data' => [
                'name' => $name,
                'last_name' => $lastName
            ]
        ];
    }

    public function getKey ($name, $lastname)
    {
        $base = "{$name}{$lastname[0]}";
        $try = $base;
        $i = 0;
       while (DB::table('creators')->where('key', $try)->exists()) {
           $try = $base . $i++;
       }
       return strtolower($try);
    }
}
