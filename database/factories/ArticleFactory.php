<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $h2 = $this->faker->sentence();
        $p = $this->faker->paragraph();
        return [
            'title' => $this->faker->sentence(),
            'html' => "<h2> $h2 <h2> <p> $p </p>",
            'delta' => [
                "ops" => [
                    ["insert" => $h2],
                    [
                        "attributes" => ['header' => 2],
                        "insert" => "\n"
                    ],
                    ["insert" => $p]
                ]
            ],
            'meta' => [
                'datos' => [
                    'data1' => 'bla bla',
                    'dato2' => 'bla bla2'
                ]
            ]

        ];
    }
}
