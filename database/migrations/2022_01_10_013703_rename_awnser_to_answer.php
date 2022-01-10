<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameAwnserToAnswer extends Migration
{

    public $old1 = 'awnsers';
    public $old2 = 'awnser_options';
    public $new1 = 'answers';
    public $new2 = 'answer_options';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename($this->old1, $this->new1);
        Schema::rename($this->old2, $this->new2);

        Schema::table($this->new2, function(Blueprint $table)
        {
            $table->renameColumn('awnser_id', 'answer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename($this->new1, $this->old1);
        Schema::rename($this->new2, $this->old2);

        Schema::table($this->old2, function(Blueprint $table)
        {
            $table->renameColumn('answer_id', 'awnser_id');
        });
    }
}
