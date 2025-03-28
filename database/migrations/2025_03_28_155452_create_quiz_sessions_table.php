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
        Schema::create('quiz_sessions', function (Blueprint $table) {
            $table->id('idquiz_sessions');
            $table->unsignedBigInteger('user_id'); 
            $table->integer('babak');
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->integer('duration')->nullable();
            $table->integer('skor')->default(0);
            $table->integer('warning_count')->default(0);
            $table->enum('status', ['in_progress', 'submitted', 'force_submitted']);
            $table->timestamps();
            $table->foreign('user_id')->references('idusers')->on('users')->onDelete('cascade');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_sessions');
    }
};
