<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Dashboard\Controllers;

use Carbon\Carbon;
use Cookie;
use FI\Http\Controllers\Controller;
use FI\Modules\Settings\Models\Setting;
use FI\Modules\Settings\Models\UserSetting;
use FI\Support\DashboardWidgets;
use Illuminate\Http\Response;

class DashboardController extends Controller
{
    public function index()
    {
        $dashboardWidgetListsByOrder = DashboardWidgets::dashboardWidgetListsByOrder();
        return view('dashboard.index')
            ->with('widgets', DashboardWidgets::listsByOrder())
            ->with('customDateRange', config('fi.dashboardWidgetsDateOption') == 'custom_date_range' ? Carbon::createFromDate(config('fi.dashboardWidgetsFromDate'))->format(config('fi.dateFormat')) . ' ' . trans('fi.to') . ' ' . Carbon::createFromDate(config('fi.dashboardWidgetsToDate'))->format(config('fi.dateFormat')) : '')
            ->with('dashboardWidgetsDateOptions', periods())->with('widgetPositionLeft', ($dashboardWidgetListsByOrder['left'] != null) ? array_values($dashboardWidgetListsByOrder['left']) : null)
            ->with('widgetPositionRight', ($dashboardWidgetListsByOrder['right'] != null) ? array_values($dashboardWidgetListsByOrder['right']) : null)
            ->with('widgetPositionCenter', ($dashboardWidgetListsByOrder['center'] != null) ? array_values($dashboardWidgetListsByOrder['center']) : null);
    }

    public function updateWidgetSettings()
    {
        UserSetting::saveByKey('dashboardWidgetsDateOption', request('dashboardWidgetsDateOption'), auth()->user());
        if (request('dashboardWidgetsDateOption') == 'custom_date_range')
        {
            if (request('dashboardWidgetsFromDate'))
            {
                $fromDate = Carbon::createFromFormat(config('fi.dateFormat'), request('dashboardWidgetsFromDate'))->format('Y-m-d');
                UserSetting::saveByKey('dashboardWidgetsFromDate', $fromDate, auth()->user());
            }
            if (request('dashboardWidgetsToDate'))
            {
                $toDate = Carbon::createFromFormat(config('fi.dateFormat'), request('dashboardWidgetsToDate'))->format('Y-m-d');
                UserSetting::saveByKey('dashboardWidgetsToDate', $toDate, auth()->user());
            }
        }
        else
        {
            UserSetting::saveByKey('dashboardWidgetsFromDate', '', auth()->user());
            UserSetting::saveByKey('dashboardWidgetsToDate', '', auth()->user());
        }
    }

    public function versionCheckPreference()
    {
        Cookie::queue(Cookie::forever('versionCheck', 0));
        Cookie::queue(Cookie::forever('versionCheckDate', Carbon::now()));
        Cookie::queue(Cookie::forget('versionAlert'));
    }

    public function agreementCheckPreference()
    {
        Cookie::queue(Cookie::forever('agreementCheck', 0));
        Cookie::queue(Cookie::forever('agreementCheckDate', Carbon::now()));
        session(['agreementExpireAlert' => '']);
        session(['agreementExpiredAlert' => '']);
    }

    public function widgetColumnPosition($key = null, $value = null)
    {
        $key = 'widgetColumnWidth' . $key;
        refactorClientColumnSetting($key, $value);
    }

    public function widgetPosition()
    {
        $widgetPositions = request()->except('KpiCards', 'widgetPosition', 'widgetColumnPosition');
        (request('widgetPosition') != null) ? (($widgetPositions != null) ? $widgetPositions['widgetPosition' . ucfirst(request('widgetPosition'))] = json_encode(array_keys($widgetPositions)) : $widgetPositions['widgetPosition' . ucfirst(request('widgetPosition'))] = null) : null;
        foreach ($widgetPositions as $key => $value)
        {
            if ($key != 'widgetPosition' . ucfirst(request('widgetPosition')))
            {
                self::widgetColumnPosition($key, request('widgetColumnPosition'));
            }
            $key = ($key == 'widgetPosition' . ucfirst(request('widgetPosition'))) ? $key : 'widgetDisplayOrder' . $key;
            refactorClientColumnSetting($key, $value);
        }
    }

    public function keyAndVersionCheck()
    {
        $key      = Setting::getByKey('key');
        $response = fiAgreementCheck(auth()->user(), $key);

        return response()->json(['success' => $response], Response::HTTP_OK);
    }

    public function applicationClean()
    {
        return view('dashboard._modal_app_clear_up')
            ->with('action', request('action'))
            ->with('url', request('url'))
            ->with('method', request('method'))
            ->with('delete', request('delete'))
            ->with('messageHide', request('messageHide', false))
            ->with('message', request('message'));
    }
}