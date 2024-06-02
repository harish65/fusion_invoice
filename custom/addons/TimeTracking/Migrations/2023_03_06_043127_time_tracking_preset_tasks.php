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

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('time_tracking_preset_tasks'))
        {
            Schema::create('time_tracking_preset_tasks', function (Blueprint $table)
            {
                $table->increments('id');
                $table->string('list_name')->nullable();
                $table->timestamps();
            });
        }
    }
};
