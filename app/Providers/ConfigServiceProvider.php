<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        try
        {
            $settings = DB::table('settings')->whereSettingKey('key')->pluck('setting_value');
            config(['app.key' => isset($settings[0]) ? $settings[0] : 'TempKeyDuringInstallation1234567']);
        }
        catch (\Exception)
        {
            config(['app.key' => 'TempKeyDuringInstallation1234567']);
        }

    }
}
