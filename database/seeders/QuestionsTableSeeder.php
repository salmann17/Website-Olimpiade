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
        Question::create([
            'babak' => 1,
            'type' => 'multiple_choice',
            'question' => 'Apa yang dimaksud dengan inflasi?',
            'pilihan_a' => 'Penurunan harga barang',
            'pilihan_b' => 'Kenaikan harga secara umum',
            'pilihan_c' => 'Kenaikan upah minimum',
            'pilihan_d' => 'Penurunan nilai mata uang asing',
            'correct_answer' => 'B',
        ]);

        Question::create([
            'babak' => 2,
            'type' => 'true_false',
            'question' => 'Pajak adalah sumber pendapatan negara.',
            'correct_answer' => 'true',
        ]);

        Question::create([
            'babak' => 3,
            'type' => 'text_input',
            'question' => 'Sebutkan salah satu jenis pajak tidak langsung!',
        ]);
    }
}
