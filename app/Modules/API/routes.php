<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['prefix' => 'api', 'middleware' => 'before.middleware', 'namespace' => 'FI\Modules\API\Controllers'], function ()
{
    //API Version v1
    Route::group(['prefix' => 'v1'], function ()
    {

        Route::post('login', ['uses' => 'ApiAuthController@login']);

        Route::group(['middleware' => ['auth:sanctum']], function ()
        {

            Route::group(['prefix' => 'clients'], function ()
            {
                Route::get('/', ['uses' => 'ApiClientController@index'])->middleware('can:clients.view');
                Route::get('{id}', ['uses' => 'ApiClientController@show'])->middleware('can:clients.view');
                Route::post('store', ['uses' => 'ApiClientController@store'])->middleware('can:clients.create');
                Route::put('{id}', ['uses' => 'ApiClientController@update'])->middleware('can:clients.update');
                Route::delete('{id}', ['uses' => 'ApiClientController@delete'])->middleware('can:clients.delete');
                Route::patch('custom-fields/add', ['uses' => 'ApiClientController@addUpdateCustomFields'])->middleware('can:clients.update');
            });

            Route::group(['prefix' => 'quotes'], function ()
            {
                Route::get('/', ['uses' => 'ApiQuoteController@index'])->middleware('can:quotes.view');
                Route::get('{id}', ['uses' => 'ApiQuoteController@show'])->middleware('can:quotes.view');
                Route::post('store', ['uses' => 'ApiQuoteController@store'])->middleware('can:quotes.create');
                Route::put('items/add', ['uses' => 'ApiQuoteController@addItem'])->middleware('can:quotes.update');
                Route::delete('{id}', ['uses' => 'ApiQuoteController@delete'])->middleware('can:quotes.delete');
                Route::post('email', ['uses' => 'ApiQuoteController@sendMail'])->middleware('can:quotes.update');
                Route::patch('custom-fields/add', ['uses' => 'ApiQuoteController@addUpdateCustomFields'])->middleware('can:quotes.update');
                Route::get('{id}/pdf', ['uses' => 'ApiQuoteController@pdf'])->middleware('can:quotes.view');
            });

            Route::group(['prefix' => 'invoices'], function ()
            {
                Route::get('/', ['uses' => 'ApiInvoiceController@index'])->middleware('can:invoices.view');
                Route::get('{id}', ['uses' => 'ApiInvoiceController@show'])->middleware('can:invoices.view');
                Route::post('store', ['uses' => 'ApiInvoiceController@store'])->middleware('can:invoices.create');
                Route::put('items/add', ['uses' => 'ApiInvoiceController@addItem'])->middleware('can:invoices.update');
                Route::delete('{id}', ['uses' => 'ApiInvoiceController@delete'])->middleware('can:invoices.delete');
                Route::post('email', ['uses' => 'ApiInvoiceController@sendMail'])->middleware('can:invoices.update');
                Route::patch('custom-fields/add', ['uses' => 'ApiInvoiceController@addUpdateCustomFields'])->middleware('can:invoices.update');
                Route::get('{id}/pdf', ['uses' => 'ApiInvoiceController@pdf'])->middleware('can:invoices.view');
                Route::post('apply/credit-memo', ['uses' => 'ApiInvoiceController@applyCreditMemo'])->middleware('can:invoices.view');
            });

            Route::group(['prefix' => 'payments'], function ()
            {
                Route::get('/', ['uses' => 'ApiPaymentController@index'])->middleware('can:payments.view');
                Route::get('{id}', ['uses' => 'ApiPaymentController@show'])->middleware('can:payments.view');
                Route::post('store', ['uses' => 'ApiPaymentController@store'])->middleware('can:payments.create');
                Route::delete('{id}', ['uses' => 'ApiPaymentController@delete'])->middleware('can:payments.delete');
                Route::patch('custom-fields/add', ['uses' => 'ApiPaymentController@addUpdateCustomFields'])->middleware('can:payments.update');
            });

            Route::post('logout', ['uses' => 'ApiAuthController@logout']);
        });


        Route::group(['prefix' => 'fi-check'], function ()
        {
            Route::get('addons', ['uses' => 'ApiFiCheckController@getActiveAddons']);
            Route::get('version', ['uses' => 'ApiFiCheckController@getLatestVersion']);
            Route::get('migration', ['uses' => 'ApiFiCheckController@getLatestMigration']);
        });

    });
});