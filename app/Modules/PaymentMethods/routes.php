<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\PaymentMethods\Controllers'], function ()
{
    Route::group(['prefix' => 'payment_methods'], function ()
    {
        Route::get('/', ['uses' => 'PaymentMethodController@index', 'as' => 'paymentMethods.index'])->middleware('can:payment_methods.view');
        Route::get('create', ['uses' => 'PaymentMethodController@create', 'as' => 'paymentMethods.create'])->middleware('can:payment_methods.create');
        Route::get('{paymentMethod}/edit', ['uses' => 'PaymentMethodController@edit', 'as' => 'paymentMethods.edit'])->middleware('can:payment_methods.update');
        Route::get('{paymentMethod}/delete', ['uses' => 'PaymentMethodController@delete', 'as' => 'paymentMethods.delete'])->middleware('can:payment_methods.delete');

        Route::post('store', ['uses' => 'PaymentMethodController@store', 'as' => 'paymentMethods.store'])->middleware('can:payment_methods.create');
        Route::post('update/{paymentMethod}', ['uses' => 'PaymentMethodController@update', 'as' => 'paymentMethods.update'])->middleware('can:payment_methods.update');

        Route::post('delete/confirmation/modal', ['uses' => 'PaymentMethodController@deleteModal', 'as' => 'payment.methods.delete.modal'])->middleware('can:payment_methods.delete');
    });
});