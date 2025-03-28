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
            $table->id('idquestions');
            $table->integer('babak');
            $table->enum('type', ['multiple_choice', 'true_false', 'text_input']);
            $table->longText('question');
            $table->longText('pilihan_a')->nullable();
            $table->longText('pilihan_b')->nullable();
            $table->longText('pilihan_c')->nullable();
            $table->longText('pilihan_d')->nullable();
            $table->string('correct_answer', 45)->nullable();
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
