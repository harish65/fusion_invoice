<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web'], 'namespace' => 'FI\Modules\Tasks\Controllers'], function ()
{
    Route::group(['prefix' => 'tasks'], function ()
    {
        Route::get('run', ['uses' => 'TaskController@run', 'as' => 'tasks.run']);
        Route::get('generate-timeline-history', ['uses' => 'TaskController@generateTimelineHistory', 'as' => 'tasks.generate_timeline_history']);
        Route::get('test-layout', ['uses' => 'TaskController@testLayout', 'as' => 'tasks.test_layout']);
    });

    Route::group(['middleware' => ['auth.admin']], function ()
    {
        Route::group(['prefix' => 'recurring_invoices'], function ()
        {
            Route::post('create-live-invoice', ['uses' => 'FI\Modules\Tasks\Controllers\TaskController@createLiveInvoice', 'as' => 'recurringInvoices.create.live.invoice'])->middleware('can:recurring_invoices.delete');
        });
    });
});
