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

class TimeTrackingInstall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('time_tracking_projects'))
        {
            Schema::create('time_tracking_projects', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();
                $table->integer('company_profile_id');
                $table->integer('user_id');
                $table->integer('client_id');
                $table->string('name');
                $table->decimal('hourly_rate');
                $table->integer('status_id');
                $table->index('client_id');
                $table->index('company_profile_id');
                $table->index('user_id');
            });
        }

        if (!Schema::hasTable('time_tracking_tasks'))
        {
            Schema::create('time_tracking_tasks', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();
                $table->integer('time_tracking_project_id');
                $table->string('name');
                $table->tinyInteger('display_order');
                $table->boolean('billed');
                $table->integer('invoice_id');

                $table->index('time_tracking_project_id');
                $table->index('invoice_id');
            });
        }

        if (!Schema::hasTable('time_tracking_timers'))
        {
            Schema::create('time_tracking_timers', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();
                $table->integer('time_tracking_task_id');
                $table->timestamp('start_at');
                $table->timestamp('end_at');
                $table->decimal('hours');
                $table->text('description');
                $table->index('time_tracking_task_id');
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
        if (Schema::hasTable('time_tracking_projects'))
        {
            Schema::drop('time_tracking_projects');
        }
        if (Schema::hasTable('time_tracking_tasks'))
        {
            Schema::drop('time_tracking_tasks');
        }
        if (Schema::hasTable('time_tracking_timers'))
        {
            Schema::drop('time_tracking_timers');
        }
    }
}
