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
            $table->dropColumn('last_played_won');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_game_stats', function (Blueprint $table) {
            $table->boolean('last_played_won')->nullable()->after('last_played_date');
        });
    }
};
