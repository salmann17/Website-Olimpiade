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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_schedule_id')->constrained('quiz_schedules')->onDelete('cascade');
            $table->enum('type', ['multiple_choice', 'true_false', 'text_input']);
            $table->longText('question');
            $table->longText('pilihan_a')->nullable();
            $table->longText('pilihan_b')->nullable();
            $table->longText('pilihan_c')->nullable();
            $table->longText('pilihan_d')->nullable();
            $table->string('correct_answer', 1)->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
