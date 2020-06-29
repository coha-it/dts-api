<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OnDeleteCascade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign('questions_survey_id_foreign');
            $table->foreign('survey_id')->references('id')->on('surveys')->onDelete('cascade');
        });

        Schema::table('question_options', function (Blueprint $table) {
            $table->dropForeign('question_options_question_id_foreign');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign('questions_survey_id_foreign');
            $table->foreign('survey_id')->references('id')->on('surveys');
        });

        Schema::table('question_options', function (Blueprint $table) {
            $table->dropForeign('question_options_question_id_foreign');
            $table->foreign('question_id')->references('id')->on('questions');
        });
    }
}
