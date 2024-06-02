<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class BringBackSettingResetInvoiceDateEmailDraft extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $setting = DB::table('settings')->where('setting_key', 'resetInvoiceDateEmailDraft')->first();

        if ($setting == null)
        {
            Setting::saveByKey('resetInvoiceDateEmailDraft', '0');
        }
    }
}
