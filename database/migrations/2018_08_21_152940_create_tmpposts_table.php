<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmppostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmpposts', function (Blueprint $table) {
            $table->string('id', 8);
            $table->string('title', 300);
            $table->string('author', 20);
            $table->integer('score');
            $table->timestamps()->useCurrent();

            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tmpposts');
    }
}
