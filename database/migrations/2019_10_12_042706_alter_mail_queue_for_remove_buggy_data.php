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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterMailQueueForRemoveBuggyData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('mail_queue', 'mailable_type'))
        {
            if (DB::table('mail_queue')->where('mailable_type', 'FI\Modules\Settings\Models\Setting')->count() > 0)
            {
                DB::table('mail_queue')->where('mailable_type', 'FI\Modules\Settings\Models\Setting')->delete();
            }
        }
    }

}
