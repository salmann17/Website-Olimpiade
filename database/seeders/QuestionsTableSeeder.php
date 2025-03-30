<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $scheduleIds = [1, 2, 3];
        $questionTypes = ['multiple_choice', 'true_false', 'text_input'];

        foreach ($scheduleIds as $scheduleId) {
            $total = match($scheduleId) {
                1 => 10,
                2 => 15,
                3 => 20,
            };

            for ($i = 1; $i <= $total; $i++) {
                $type = $questionTypes[array_rand($questionTypes)];

                Question::create([
                    'quiz_schedule_id' => $scheduleId,
                    'type' => $type,
                    'question' => "Soal ke-$i untuk babak $scheduleId",
                    'pilihan_a' => $type === 'multiple_choice' ? 'Pilihan A' : null,
                    'pilihan_b' => $type === 'multiple_choice' ? 'Pilihan B' : null,
                    'pilihan_c' => $type === 'multiple_choice' ? 'Pilihan C' : null,
                    'pilihan_d' => $type === 'multiple_choice' ? 'Pilihan D' : null,
                    'correct_answer' => match($type) {
                        'multiple_choice' => ['A', 'B', 'C', 'D'][rand(0, 3)],
                        'true_false' => 'true',
                        default => null
                    }
                ]);
            }
        }
    }
}
