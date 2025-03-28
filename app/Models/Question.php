<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'questions';
    protected $primaryKey = 'idquestions';

    protected $fillable = [
        'babak',
        'type',
        'question',
        'pilihan_a',
        'pilihan_b',
        'pilihan_c',
        'pilihan_d',
        'correct_answer',
    ];

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class, 'question_id');
    }
}
