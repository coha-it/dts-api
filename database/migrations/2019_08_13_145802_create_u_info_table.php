<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUInfoTable extends Migration
{

    public $tblnm = 'u_infos';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tblnm, function (Blueprint $table) {
            // Table Columns
            // $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->unique()->index();
            $table->string('firstname');
            $table->string('lastname');
            $table->timestamps();
        });

        Schema::table($this->tblnm, function (Blueprint $table) {
            // Connect Foreign Key
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tblnm);
    }
}
