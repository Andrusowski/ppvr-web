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
        Schema::table('users', function (Blueprint $table) {
            // Make existing columns nullable for OAuth users
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();

            // Add osu! OAuth fields
            $table->unsignedBigInteger('osu_id')->unique()->nullable()->after('id');
            $table->string('avatar_url')->nullable()->after('name');
            $table->text('access_token')->nullable()->after('password');
            $table->text('refresh_token')->nullable()->after('access_token');
            $table->timestamp('token_expires_at')->nullable()->after('refresh_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'osu_id',
                'avatar_url',
                'access_token',
                'refresh_token',
                'token_expires_at',
            ]);

            // Revert nullable changes
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};
