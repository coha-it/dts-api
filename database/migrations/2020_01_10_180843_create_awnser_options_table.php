<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAwnserOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('awnser_options', function (Blueprint $table) {
            // IDs
            $table->bigIncrements('id');
            $table->bigInteger('awnser_id')->unsigned()->index();
            $table->bigInteger('option_id')->unsigned()->index();
        });

        Schema::table('awnser_options', function (Blueprint $table) {
            // Connect Foreign Key
            $table->foreign('awnser_id')->references('id')->on('awnsers')->onDelete('cascade');
            $table->foreign('option_id')->references('id')->on('question_options')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('awnser_options');
    }
}
