<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Widgets\Dashboard\KpiCards\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class WidgetServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Register the view path.
        view()->addLocation(app_path('Widgets/Dashboard/KpiCards/Views'));

        // Register the widget view composer.
        view()->composer('KpiCardsWidget', 'FI\Widgets\Dashboard\KpiCards\Composers\KpiCardsWidgetComposer');

        // Widgets don't have route files so we'll place this here.
        Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Widgets\Dashboard\KpiCards\Controllers'], function ()
        {
            Route::post('widgets/dashboard/kpi_cards/render_partial', ['uses' => 'WidgetController@renderPartial', 'as' => 'widgets.dashboard.KpiCards.renderPartial']);
        });
    }

    public function register()
    {
        //
    }
}