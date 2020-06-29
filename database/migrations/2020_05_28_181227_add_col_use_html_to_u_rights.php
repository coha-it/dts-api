<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColUseHtmlToURights extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('u_rights', function (Blueprint $table) {
            // Use HTML
            $table->boolean('use_html')->after('create_users')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('u_rights', function (Blueprint $table) {
            // Use HTML
            $table->dropColumn('use_html');
        });
    }
}
