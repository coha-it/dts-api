<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveIdsOnForeignTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_user', function (Blueprint $table) {
            $table->dropColumn('id');
        });
        Schema::table('survey_group', function (Blueprint $table) {
            $table->dropColumn('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_user', function (Blueprint $table) {
            // IDs
            $table->bigIncrements('id')->before('user_id');
        });

        Schema::table('survey_group', function (Blueprint $table) {
            // IDs
            $table->bigIncrements('id')->before('group_id');
        });
    }
}
