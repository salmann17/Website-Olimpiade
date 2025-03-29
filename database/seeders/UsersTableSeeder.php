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
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'fullname' => 'Administrator',
        ]);

        User::create([
            'username' => 'peserta',
            'password' => Hash::make('peserta123'),
            'role' => 'peserta',
            'fullname' => 'Peserta',
        ]);
    }
}
