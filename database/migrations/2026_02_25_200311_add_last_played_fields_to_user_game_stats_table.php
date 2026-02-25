<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_game_stats', function (Blueprint $table) {
            $table->date('last_played_date')->nullable()->after('round_breakdown');
            $table->string('last_played_result', 10)->nullable()->after('last_played_date');
            $table->unsignedInteger('last_played_round')->nullable()->after('last_played_result');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_game_stats', function (Blueprint $table) {
            $table->dropColumn(['last_played_date', 'last_played_result', 'last_played_round']);
        });
    }
};
