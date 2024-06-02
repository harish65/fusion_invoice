<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\Settings\Controllers'], function ()
{
    Route::get('settings', ['uses' => 'SettingController@index', 'as' => 'settings.index'])->middleware('can:settings.view');
    Route::post('settings', ['uses' => 'SettingController@update', 'as' => 'settings.update'])->middleware('can:settings.update');
    Route::get('settings/update_check', ['uses' => 'SettingController@updateCheck', 'as' => 'settings.updateCheck'])->middleware('can:settings.update');
    Route::get('settings/logo/delete', ['uses' => 'SettingController@logoDelete', 'as' => 'settings.logo.delete'])->middleware('can:settings.update');
    Route::post('settings/save_tab', ['uses' => 'SettingController@saveTab', 'as' => 'settings.saveTab'])->middleware('can:settings.update');
    Route::get('settings/pdf/delete', ['uses' => 'SettingController@pdfCleanup', 'as' => 'settings.pdf.cleanup']);
    Route::get('settings/cache/clear', ['uses' => 'SettingController@cacheCleanup', 'as' => 'settings.cache.cleanup']);
    Route::post('settings/key/update', ['uses' => 'SettingController@verifyAndUpdateKey', 'as' => 'settings.key.update']);
    Route::post('settings/generate-timeline/modal', ['uses' => 'SettingController@generateTimelineModal', 'as' => 'settings.generate.timeline.modal'])->middleware('can:settings.update');


    Route::get('settings/system-default-dashboard', ['uses' => 'SettingController@indexSystemDefaultDashboard', 'as' => 'settings.system.default.dashboard.index']);
    Route::post('settings/system-default-dashboard', ['uses' => 'SettingController@updateSystemDefaultDashboard', 'as' => 'settings.system.default.dashboard.update']);
    Route::get('settings/user-specific-dashboard-index', ['uses' => 'SettingController@indexUserSpecificDashboards', 'as' => 'settings.user.specific.dashboard.index']);
    Route::get('settings/{id}/user-specific-dashboard-settings', ['uses' => 'SettingController@indexUserSpecificSettings', 'as' => 'settings.user.specific.dashboard.settings']);
    Route::post('settings/{id}/user-specific-dashboard', ['uses' => 'SettingController@updateUserSpecificDashboards', 'as' => 'settings.user.specific.dashboard.update']);

    if (!config('app.demo'))
    {
        Route::get('backup/database', ['uses' => 'BackupController@database', 'as' => 'settings.backup.database'])->middleware('can:settings.update');
    }

    Route::group(['prefix' => 'test_mail'], function () {
        Route::post('create', ['uses' => 'TestMailController@create', 'as' => 'testMail.create'])->middleware('can:settings.update');
        Route::post('store', ['uses' => 'TestMailController@store', 'as' => 'testMail.store'])->middleware('can:settings.update');
    });
});