<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SurveysChangeTextColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('surveys', function (Blueprint $table) {
            // Texting
            $table->string(     'author', 500   )->nullable(true)->change();
            $table->string(     'title', 500    )->nullable(true)->change();
            $table->text(       'desc_short'    )->nullable(true)->change();
            $table->mediumText( 'desc_long'     )->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('surveys', function (Blueprint $table) {
            // $table->string('author')->nullable()->change();
            // $table->string('title')->nullable()->change();
            // $table->string('desc_short')->nullable()->change();
            // $table->string('desc_long')->nullable()->change();
        });
    }
}
