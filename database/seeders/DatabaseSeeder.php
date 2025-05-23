<?php

namespace Database\Seeders;

use App\Models\QuizSchedule;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            QuizScheduleSeeder::class,
            UsersTableSeeder::class,
            QuestionsTableSeeder::class,
            QuizSessionsTableSeeder::class,
            QuizAnswerTableSeeder::class,
        ]);
    }
}
