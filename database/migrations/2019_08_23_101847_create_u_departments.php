<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUDepartments extends Migration
{
    public $tbl = 'u_departments';

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

        // Add to Users
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('department_id')->nullable()->unsigned();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('department_id')->references('id')->on($this->tbl);
        });
    }
    
                        /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_department_id_foreign');
            $table->dropColumn('department_id');
        });

        Schema::dropIfExists($this->tbl);
    }
}
