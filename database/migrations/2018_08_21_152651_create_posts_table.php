<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->string('id', 8)->primary();
            $table->integer('player_id')->unsigned()->index();
            $table->string('map_artist', 255);
            $table->string('map_title', 255);
            $table->string('map_diff', 255);
            $table->string('author', 20)->index();
            $table->integer('score')->index();
            $table->integer('ups')->unsigned();
            $table->integer('downs')->unsigned();
            $table->integer('silver')->unsigned()->nullable();
            $table->integer('gold')->unsigned();
            $table->integer('platinum')->unsigned()->nullable();
            $table->integer('created_utc')->unsigned();
            $table->boolean('final');
            $table->timestamp('created_at')->useCurrent()->index();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('player_id')->references('id')->on('players');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
