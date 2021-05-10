<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(CreatorSeeder::class);
        $this->call(SourceSeeder::class);
        $this->call(ArticleSeeder::class);
    }

}
