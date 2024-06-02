<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Widgets\Dashboard\SalesChart\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Settings\Models\Setting;
use FI\Modules\Settings\Models\UserSetting;

class WidgetController extends Controller
{
    public function renderPartial()
    {
        Setting::saveByKey('dashboardWidgetsDateOption', request('dashboardWidgetsDateOption'));

        if (request()->has('dashboardWidgetsFromDate'))
        {
            Setting::saveByKey('dashboardWidgetsFromDate', request('dashboardWidgetsFromDate'));
        }

        if (request()->has('dashboardWidgetsToDate'))
        {
            Setting::saveByKey('dashboardWidgetsToDate', request('dashboardWidgetsToDate'));
        }

        Setting::setAll();
        UserSetting::setAll(auth()->user());

        return view('SalesChartWidget');
    }

    public function widgetUpdateSalesChartSetting($invoiceStatus = null)
    {

        Setting::saveByKey('widgetSalesChartSetting', (isset($invoiceStatus) && $invoiceStatus != null) ? $invoiceStatus : 1);

        return back();
    }

    public function widgetUpdateSalesChartAccumulateSetting($invoiceAccumulateStatus = 0)
    {
        UserSetting::saveByKey('accumulateTotals', $invoiceAccumulateStatus, auth()->user());

        return back();
    }
}