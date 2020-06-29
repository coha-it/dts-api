<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserCompanyCol extends Migration
{
    public $tbl = 'users';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tbl, function (Blueprint $table) {
            $table->bigInteger('company_id')->nullable()->unsigned();
        });

        Schema::table($this->tbl, function (Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('u_companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->tbl, function (Blueprint $table) {
            $table->dropForeign('users_company_id_foreign');
            $table->dropColumn('company_id');
        });
    }
}
