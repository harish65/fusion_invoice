<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Widgets\Dashboard\OpenInvoiceAging\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class WidgetServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Register the view path.
        view()->addLocation(app_path('Widgets/Dashboard/OpenInvoiceAging/Views'));

        // Register the widget view composer.
        view()->composer('OpenInvoiceAgingWidget', 'FI\Widgets\Dashboard\OpenInvoiceAging\Composers\OpenInvoicesWidgetComposer');

        Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Widgets\Dashboard\OpenInvoiceAging\Controllers'], function ()
        {
            Route::get('widgets/dashboard/open_invoice_aging/{invoiceStatus}', ['uses' => 'WidgetController@widgetUpdateOpenInvoiceAgingSetting', 'as' => 'widgets.dashboard.OpenInvoiceAging.widgetUpdateOpenInvoiceAgingSetting']);
        });
    }

    public function register()
    {
        //
    }
}