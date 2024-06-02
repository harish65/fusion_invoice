<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['prefix' => 'system_log', 'middleware' => 'web', 'namespace' => 'FI\Modules\SystemLog\Controllers'], function ()
{
    Route::get('clear/modal', ['uses' => 'SystemLogController@systemLogClearModal', 'as' => 'systemLog.clear.modal'])->middleware('can:system_logs.view');
    Route::post('clear', ['uses' => 'SystemLogController@systemLogClear', 'as' => 'systemLog.clear'])->middleware('can:system_logs.view');
});

Route::group(['prefix' => 'system_log', 'middleware' => 'web'], function ()
{
    Route::get('/', ['uses' => 'Rap2hpoutre\LaravelLogViewer\LogViewerController@index', 'as' => 'systemLog.index'])->middleware('can:system_logs.view');
});