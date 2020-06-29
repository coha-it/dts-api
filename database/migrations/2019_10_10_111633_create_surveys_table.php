<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('created_by')->unsigned()->index()->nullable();
            $table->boolean('active')->default(true);

            // Texting
            $table->string('author')->nullable();
            $table->string('title')->nullable();
            $table->string('desc_short')->nullable();
            $table->string('desc_long')->nullable();

            // Dates and Lockings
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->boolean('is_finished')->default(false);
            $table->boolean('is_canceled')->default(false);

            // Rights and viewability
            $table->boolean('only_editable_by_creator')->default(true);
            $table->boolean('is_public')->default(false);

            // Timestamps and Deletings
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('surveys', function (Blueprint $table) {
            // Connect Foreign Key
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
        Schema::dropIfExists('surveys');
    }
}
