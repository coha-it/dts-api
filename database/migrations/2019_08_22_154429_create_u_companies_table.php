<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUCompaniesTable extends Migration
{
    public $tbl = 'u_companies';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tbl, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->boolean('public')->default(false);
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::table($this->tbl, function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tbl);
    }
}
