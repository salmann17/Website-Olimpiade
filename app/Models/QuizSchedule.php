<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_round',
        'start_time',
        'end_time',
        'duration',
        'total_questions',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function quizSessions()
    {
        return $this->hasMany(QuizSession::class);
    }
}
