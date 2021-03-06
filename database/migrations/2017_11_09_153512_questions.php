<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Questions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
             $table->increments('id');
             $table->string('uuid');
             $table->string("question")->nullable();
             $table->string("options")->nullable();
             $table->boolean("require")->default(false);
             $table->string("type")->nullable();
             $table->string("answer",500)->nullable();
             $table->timestamps();
         });
        //
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('questions');
    }
}
