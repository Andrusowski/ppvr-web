<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_game_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('games_played')->default(0);
            $table->unsignedInteger('total_correct_rounds')->default(0);
            $table->unsignedInteger('current_streak')->default(0);
            $table->unsignedInteger('max_streak')->default(0);
            $table->json('round_breakdown')->nullable(); // Array of counts for each round (0-10)
            $table->date('last_played_date')->nullable();
            $table->string('last_played_result', 10)->nullable(); // 'won' or 'lost'
            $table->unsignedInteger('last_played_round')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_game_stats');
    }
};
