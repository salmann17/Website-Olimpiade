<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quiz_answer', function (Blueprint $table) {
            $table->id('idquiz_answers');
            $table->foreignId('session_id')->constrained('quiz_sessions', 'idquiz_sessions')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions', 'idquestions')->onDelete('cascade');
            $table->longText('answer')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_answer');
    }
};
