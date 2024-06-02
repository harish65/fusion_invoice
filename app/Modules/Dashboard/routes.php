<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\Dashboard\Controllers'], function ()
{
    Route::get('/', ['uses' => 'DashboardController@index', 'as' => 'dashboard.index']);
    Route::post('updatewidgets', ['uses' => 'DashboardController@updateWidgetSettings', 'as' => 'dashboard.updateWidgetSettings'])->middleware('can:allow_time_period_change.view');
    Route::get('dashboard', ['uses' => 'DashboardController@index', 'as' => 'dashboard.index']);
    Route::get('dashboard/version/check/disable', ['uses' => 'DashboardController@versionCheckPreference', 'as' => 'dashboard.version.check.preference']);
    Route::get('dashboard/agreement/check/dismiss', ['uses' => 'DashboardController@agreementCheckPreference', 'as' => 'dashboard.agreement.check.preference']);
    Route::post('dashboard/widget/position', ['uses' => 'DashboardController@widgetPosition', 'as' => 'dashboard.widget.position']);
    Route::post('fi/key/version/check', ['uses' => 'DashboardController@keyAndVersionCheck', 'as' => 'fi.agreement.check']);
    Route::post('dashboard/application/clean', ['uses' => 'DashboardController@applicationClean', 'as' => 'application.clean']);
});