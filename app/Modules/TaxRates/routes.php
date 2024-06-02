<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\TaxRates\Controllers'], function ()
{
    Route::group(['prefix' => 'tax_rates'], function ()
    {
        Route::get('/', ['uses' => 'TaxRateController@index', 'as' => 'taxRates.index'])->middleware('can:tax_rates.view');
        Route::get('create', ['uses' => 'TaxRateController@create', 'as' => 'taxRates.create'])->middleware('can:tax_rates.create');
        Route::get('{taxRate}/edit', ['uses' => 'TaxRateController@edit', 'as' => 'taxRates.edit'])->middleware('can:tax_rates.update');
        Route::get('{taxRate}/delete', ['uses' => 'TaxRateController@delete', 'as' => 'taxRates.delete'])->middleware('can:tax_rates.delete');

        Route::post('store', ['uses' => 'TaxRateController@store', 'as' => 'taxRates.store'])->middleware('can:tax_rates.create');
        Route::post('update/{taxRate}', ['uses' => 'TaxRateController@update', 'as' => 'taxRates.update'])->middleware('can:tax_rates.update');

        Route::post('delete/confirmation/modal', ['uses' => 'TaxRateController@deleteModal', 'as' => 'tax.rates.delete.modal'])->middleware('can:tax_rates.delete');
    });
});