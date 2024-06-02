<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Addons\TimeTracking\Models\TimeTrackingProject;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConvertTimeTrackingStatuses extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('time_tracking_projects', 'status'))
        {
            Schema::table('time_tracking_projects', function (Blueprint $table)
            {
                $table->enum('status', ['active', 'inactive', 'canceled']);
            });
        }

        if (Schema::hasColumn('time_tracking_projects', 'status_id'))
        {
            TimeTrackingProject::where('status_id', 1)->update(['status' => 'active']);
            TimeTrackingProject::where('status_id', 2)->update(['status' => 'inactive']);
            TimeTrackingProject::where('status_id', 3)->update(['status' => 'canceled']);

            Schema::table('time_tracking_projects', function (Blueprint $table)
            {
                $table->dropColumn(['status_id']);
            });
        }
    }

}
