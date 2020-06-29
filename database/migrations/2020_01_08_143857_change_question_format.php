<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeQuestionFormat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            // ID
            $table->string('format')
                    ->after('question_format_id')
                    ->index()
                    ->nullable();

            // Drop old Col
            $table->dropColumn('question_format_id');
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
            // ID
            $table->bigInteger('question_format_id')
                    ->after('format')
                    ->unsigned()
                    ->index()
                    ->nullable();

            // Drop old Col
            $table->dropColumn('format');
        });
    }
}
