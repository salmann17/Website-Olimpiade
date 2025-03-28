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
            'password' => md5('root'),
            'fullname' => 'Admin Root',
            'role' => 'admin'
        ]);

        User::create([
            'username' => 'peserta',
            'password' => md5('root'),
            'fullname' => 'Peserta Root',
            'role' => 'peserta'
        ]);
    }
}
