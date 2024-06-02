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
use FI\Modules\Users\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        $kpiCardsSettings = User::userDefaultSetting('widgetKpiCards');
        $widgetSettings   = array_merge($kpiCardsSettings, ['widgetEnabledOpenInvoiceAging', 'widgetEnabledSalesChart']);
        foreach ($widgetSettings as $setting)
        {
            Setting::saveByKey($setting, 1);
        }
    }
};