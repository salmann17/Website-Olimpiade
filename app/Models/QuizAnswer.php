<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAnswer extends Model
{
    protected $table = 'quiz_answers';
    protected $primaryKey = 'id';

    protected $fillable = [
        'quiz_session_id',
        'question_id',
        'answer',
        'is_correct',
        'comment',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(QuizSession::class, 'quiz_session_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
