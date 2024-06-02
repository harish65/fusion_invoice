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

class AlterTasksForRecurringTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumns('tasks',['is_recurring','recurring_frequency','recurring_period','next_date']))
        {
            Schema::table('tasks', function (Blueprint $table)
            {
                $table->tinyInteger('is_recurring')->default(0);
                $table->integer('recurring_frequency')->nullable();
                $table->integer('recurring_period')->nullable();
                $table->date('next_date')->nullable();
            });
        }
    }

}