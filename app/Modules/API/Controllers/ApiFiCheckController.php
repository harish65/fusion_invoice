<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\API\Controllers;

use Exception;
use FI\Modules\Addons\Models\Addon;
use FI\Modules\Settings\Models\Setting;
use Illuminate\Support\Facades\DB;

class ApiFiCheckController extends ApiController
{
    public function getActiveAddons()
    {
        try
        {
            $addons = Addon::select('name')->whereEnabled(1)->get()->toArray();
            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_retrieved'), 'data' => $addons], 200);

        }
        catch (Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('fi.record_not_found')], 400);
        }

    }

    public function getLatestVersion()
    {
        try
        {
            $version = Setting::select('setting_value')->whereSettingKey('version')->first();
            $key     = Setting::select('setting_value')->whereSettingKey('key')->first();

            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_retrieved'), 'data' => ['version' => $version->setting_value, 'key' => $key->setting_value]], 200);

        }
        catch (Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('fi.record_not_found')], 400);
        }

    }

    public function getLatestMigration()
    {
        try
        {
            $migration = DB::table('migrations')->orderBy('migration', 'DESC')->first();

            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_retrieved'), 'data' => $migration], 200);

        }
        catch (Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('fi.record_not_found')], 400);
        }
    }

}