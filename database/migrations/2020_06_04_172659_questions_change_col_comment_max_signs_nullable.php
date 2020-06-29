<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class QuestionsChangeColCommentMaxSignsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->smallInteger('comment_max_signs')->nullable(true)->change();
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
            // Causing Error.
            // Not Null to Null is Ok. works fine. easy
            // Nullable to "No Null" is'nt going to work
            // So let this line be as a comment
            // $table->smallInteger('comment_max_signs')->nullable(false)->change();
        });
    }
}
