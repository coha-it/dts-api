<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUPansTable extends Migration
{

    public $tbl = 'u_pans';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tbl, function (Blueprint $table) {
            // ID
            // $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->unique()->index();

            // PAN & PIN
            $table->boolean('is_pan_user')->default(true);
            $table->string('pan', 8)->nullable()->unique();
            $table->string('pin', 8)->nullable();

            // Login Fails
            $table->tinyInteger('failed_logins')->default(0);
            $table->timestamp('locked_until')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table($this->tbl, function (Blueprint $table) {
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
        Schema::dropIfExists($this->tbl);
    }
}
