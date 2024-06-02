<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class SimpletodoInstall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('simpletodo_tasks'))
        {
            Schema::create('simpletodo_tasks', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();
                $table->date('start_at');
                $table->date('due_at');
                $table->string('title');
                $table->longText('description');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('simpletodo_tasks'))
        {
            Schema::drop('simpletodo_tasks');
        }
    }
}
