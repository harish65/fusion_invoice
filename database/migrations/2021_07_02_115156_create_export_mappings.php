<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExportMappings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('export_mappings'))
        {
            Schema::create('export_mappings', function (Blueprint $table)
            {
                $table->increments('id');
                $table->timestamps();
                $table->string('type');
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_default')->default(0);
            });
        }
    }
}
