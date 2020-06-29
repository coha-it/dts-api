<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    public $tbl = 'users';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tbl, function (Blueprint $table) {

            // ID
            $table->bigIncrements('id');
            
            // E-Mail Login
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            
            // Token
            $table->rememberToken();
            
            // Timestamps
            $table->bigInteger('created_by')->unsigned()->index()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table($this->tbl, function (Blueprint $table) {
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
        Schema::dropIfExists($this->tbl);
    }
}
