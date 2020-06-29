<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateURightsTable extends Migration
{

    public $tblnm = 'u_rights';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tblnm, function (Blueprint $table) {
            // $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->unique()->index();

            // Rights
            $table->boolean('update_own_profile')->default(false);
            $table->boolean('create_surveys')->default(false);
            $table->boolean('create_groups')->default(false);
            $table->boolean('create_users')->default(false);

            $table->boolean('admin')->default(false);

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
