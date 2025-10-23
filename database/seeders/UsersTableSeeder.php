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
            'username' => 'hello',
            'password' => Hash::make('hello'),
            'role' => 'admin',
            'fullname' => 'Super Administrator',
        ]);

        User::create([
            'username' => 'test',
            'password' => Hash::make('test'),
            'role' => 'admin',
            'fullname' => 'Admin GenBi',
        ]);
    }
}
