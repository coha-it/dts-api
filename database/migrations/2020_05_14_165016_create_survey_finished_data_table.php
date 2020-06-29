<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveyFinishedDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_finished_data', function (Blueprint $table) {
            // IDs
            $table->bigIncrements('id');
            $table->bigInteger('survey_id')->unsigned()->index()->nullable();
            $table->bigInteger('user_id')->unsigned()->index()->nullable();

            // Content
            $table->ipAddress('ip_v4')->nullable(true);
            $table->ipAddress('ip_v6')->nullable(true);
            $table->json('json_data')->nullable(true);
            $table->json('navigator')->nullable(true);

            // Timestamps
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('survey_finished_data', function (Blueprint $table) {
            // Connect Foreign Key
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('survey_id')->references('id')->on('surveys');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('survey_finished_data');
    }
}
