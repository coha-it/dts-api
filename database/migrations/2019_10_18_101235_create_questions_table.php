<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            // IDs
            $table->bigIncrements('id');
            $table->bigInteger('survey_id')->unsigned()->index();
            $table->bigInteger('created_by')->unsigned()->index()->nullable();
            $table->bigInteger('question_format_id')->unsigned()->index()->nullable();
            $table->bigInteger('question_category_id')->unsigned()->index()->nullable();
            $table->smallInteger('order')->nullable();

            // Attributes
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('description')->nullable();

            // Skipping
            $table->boolean('is_skippable')->default(0);
            $table->boolean('is_commentable')->default(1);
            $table->boolean('comment_is_required')->default(0);
            $table->boolean('comment_is_number')->default(0);
            $table->smallInteger('comment_max_signs')->default(150);

            // Selection
            $table->smallInteger('min_options')->default(1);
            $table->smallInteger('max_options')->default(1);

            // Timestamps
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('questions', function (Blueprint $table) {
            // Connect Foreign Keys
            $table->foreign('survey_id')->references('id')->on('surveys')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions');
    }
}
