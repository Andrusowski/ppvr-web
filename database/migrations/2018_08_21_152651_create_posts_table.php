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
            $table->string('id', 8);
            $table->integer('player_id')->unsigned();
            $table->string('map_artist', 255);
            $table->string('map_title', 255);
            $table->string('map_diff', 255);
            $table->string('author', 20);
            $table->integer('score');
            $table->integer('ups')->unsigned();
            $table->integer('downs')->unsigned();
            $table->integer('silver')->unsigned();
            $table->integer('gold')->unsigned();
            $table->integer('platinum')->unsigned();
            $table->integer('created_utc')->unsigned();
            $table->boolean('final');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->primary('id');

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
