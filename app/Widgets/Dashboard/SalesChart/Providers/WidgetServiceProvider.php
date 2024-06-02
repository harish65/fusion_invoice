<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Widgets\Dashboard\SalesChart\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class WidgetServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Register the view path.
        view()->addLocation(app_path('Widgets/Dashboard/SalesChart/Views'));

        view()->composer('SalesChartWidget', 'FI\Widgets\Dashboard\SalesChart\Composers\SalesChartWidgetComposer');

        Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Widgets\Dashboard\SalesChart\Controllers'], function () {
            Route::post('widgets/dashboard/sales_chart/render_partial', ['uses' => 'WidgetController@renderPartial', 'as' => 'widgets.dashboard.SalesChart.renderPartial']);
            Route::get('widgets/dashboard/sales_chart/{invoiceStatus}', ['uses' => 'WidgetController@widgetUpdateSalesChartSetting', 'as' => 'widgets.dashboard.SalesChart.widgetUpdateSalesChartSetting']);
            Route::get('widgets/dashboard/sales_chart/accumulate/{invoiceAccumulateStatus}', ['uses' => 'WidgetController@widgetUpdateSalesChartAccumulateSetting', 'as' => 'widgets.dashboard.SalesChart.widgetUpdateSalesChartAccumulateSetting']);
        });
    }

    public function register()
    {
        //
    }
}