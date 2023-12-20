<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'id' => 1,
                'name' => 'Admin',
                'email'=>'admin@example.com',
                'password' => bcrypt('password'),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],
            [
                'id' => 2,
                'name' => 'User',
                'email'=>'user@example.com',
                'password' => bcrypt('password'),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ],

        ];
        User::insert($users);

        //Assign the User  Roles
        $userAdmin = User::findOrFail(1);
        $userAdmin->assignRole('Admin');

        $userOwner = User::findOrFail(2);
        $userOwner->assignRole('User');

    }
}
