<?php

namespace Database\Seeders;

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
            ->count(5)
            ->create();
        $userA = $users[0];
        $userB = $users[1];
        $userA->email = 'agustavo87@gmail.com';
        $userB->email = 'john.doe@example.com';
        $userA->save();
        $userB->save();
    }
}
