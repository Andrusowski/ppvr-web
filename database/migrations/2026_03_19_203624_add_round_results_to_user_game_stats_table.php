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
        Schema::table('user_game_stats', function (Blueprint $table) {
            $table->unsignedInteger('last_played_correct_count')->nullable()->after('last_played_round');
            $table->json('last_played_round_results')->nullable()->after('last_played_correct_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_game_stats', function (Blueprint $table) {
            $table->dropColumn(['last_played_correct_count', 'last_played_round_results']);
        });
    }
};
