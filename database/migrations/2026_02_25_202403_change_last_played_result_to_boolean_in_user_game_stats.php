<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, add the new boolean column
        Schema::table('user_game_stats', function (Blueprint $table) {
            $table->boolean('last_played_won')->nullable()->after('last_played_date');
        });

        // Migrate existing data: 'won' -> true, 'lost' -> false
        DB::table('user_game_stats')
            ->whereNotNull('last_played_result')
            ->update([
                'last_played_won' => DB::raw("CASE WHEN last_played_result = 'won' THEN 1 ELSE 0 END"),
            ]);

        // Drop the old string column
        Schema::table('user_game_stats', function (Blueprint $table) {
            $table->dropColumn('last_played_result');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the string column
        Schema::table('user_game_stats', function (Blueprint $table) {
            $table->string('last_played_result', 10)->nullable()->after('last_played_date');
        });

        // Migrate data back: true -> 'won', false -> 'lost'
        DB::table('user_game_stats')
            ->whereNotNull('last_played_won')
            ->update([
                'last_played_result' => DB::raw("CASE WHEN last_played_won = 1 THEN 'won' ELSE 'lost' END"),
            ]);

        // Drop the boolean column
        Schema::table('user_game_stats', function (Blueprint $table) {
            $table->dropColumn('last_played_won');
        });
    }
};
