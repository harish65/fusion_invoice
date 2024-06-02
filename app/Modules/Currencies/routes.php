<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\Currencies\Controllers'], function ()
{
    Route::group(['prefix' => 'currencies'], function ()
    {
        Route::get('/', ['uses' => 'CurrencyController@index', 'as' => 'currencies.index'])->middleware('can:currencies.view');
        Route::get('create', ['uses' => 'CurrencyController@create', 'as' => 'currencies.create'])->middleware('can:currencies.create');
        Route::get('{id}/edit', ['uses' => 'CurrencyController@edit', 'as' => 'currencies.edit'])->middleware('can:currencies.update');
        Route::get('{id}/delete', ['uses' => 'CurrencyController@delete', 'as' => 'currencies.delete'])->middleware('can:currencies.delete');

        Route::post('store', ['uses' => 'CurrencyController@store', 'as' => 'currencies.store'])->middleware('can:currencies.create');
        Route::post('get-exchange-rate', ['uses' => 'CurrencyController@getExchangeRate', 'as' => 'currencies.getExchangeRate'])->middleware('can:currencies.update');
        Route::post('update/{id}', ['uses' => 'CurrencyController@update', 'as' => 'currencies.update'])->middleware('can:currencies.update');

        Route::post('delete/confirmation/modal', ['uses' => 'CurrencyController@deleteModal', 'as' => 'currencies.delete.modal'])->middleware('can:currencies.delete');
    });
});