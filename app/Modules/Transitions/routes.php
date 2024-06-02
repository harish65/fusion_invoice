<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['prefix' => 'transitions', 'middleware' => ['web', 'auth'], 'namespace' => 'FI\Modules\Transitions\Controllers'], function ()
{
    Route::group(['prefix' => 'widget'], function ()
    {
        Route::post('list', ['uses' => 'TransitionController@widgetList', 'as' => 'transitions.widget.list']);
        Route::post('refresh', ['uses' => 'TransitionController@refresh', 'as' => 'transitions.widget.refresh']);
    });
    Route::post('user-transitions/{client}', ['uses' => 'TransitionController@userTransitions', 'as' => 'user.transition.list']);
    Route::post('invoice-transitions/{invoice}', ['uses' => 'TransitionController@invoiceTransitions', 'as' => 'invoice.transition.list']);
});
