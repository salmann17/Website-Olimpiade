<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizSession extends Model
{
    protected $table = 'quiz_sessions';
    protected $primaryKey = 'idquiz_sessions';

    protected $fillable = [
        'user_id',
        'babak',
        'start_time',
        'end_time',
        'duration',
        'skor',
        'warning_count',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class, 'session_id');
    }
    public function schedule()
    {
        return $this->belongsTo(QuizSchedule::class, 'quiz_schedule_id');
    }
}
