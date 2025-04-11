<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => '54lm4nr00t',
            'password' => Hash::make('08261715'),
            'role' => 'admin',
            'fullname' => 'Super Administrator',
        ]);

        User::create([
            'username' => 'G3NB1',
            'password' => Hash::make('#Th1s1s4G3nB1'),
            'role' => 'admin',
            'fullname' => 'Admin GenBi',
        ]);
    }
}
