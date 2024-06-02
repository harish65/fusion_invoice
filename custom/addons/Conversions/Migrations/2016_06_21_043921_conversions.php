<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class Conversions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('conversions'))
        {
            Schema::create('conversions', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();
                $table->string('driver');
            });
        }
        if (!Schema::hasTable('conversion_records'))
        {
            Schema::create('conversion_records', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();
                $table->integer('conversion_id');
                $table->string('table');
                $table->integer('record_id');
            });
        }
    }

}
