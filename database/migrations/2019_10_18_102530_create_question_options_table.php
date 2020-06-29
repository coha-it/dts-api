<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_options', function (Blueprint $table) {
            // IDs
            $table->bigIncrements('id');
            $table->bigInteger('question_id')->unsigned()->index();

            // Value
            $table->float('value')->nullable();

            // Texting
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('color', 50)->nullable();

            // Softdeletes and Timestamps
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('question_options', function (Blueprint $table) {
            // Connect Foreign Key
            $table->foreign('question_id')->references('id')->on('questions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('question_options');
    }
}
