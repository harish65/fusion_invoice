<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'Addons\PricingFormula\Controllers'], function ()
{
    Route::group(['prefix' => 'item/price/formula'], function ()
    {
        Route::get('/', ['uses' => 'ItemPriceFormulaController@index', 'as' => 'item.priceFormula.index'])->middleware('can:item_categories.view');
        Route::get('create', ['uses' => 'ItemPriceFormulaController@create', 'as' => 'item.priceFormula.create'])->middleware('can:item_categories.create');
        Route::post('create', ['uses' => 'ItemPriceFormulaController@store', 'as' => 'item.priceFormula.store'])->middleware('can:item_categories.create');
        Route::get('{id}/edit', ['uses' => 'ItemPriceFormulaController@edit', 'as' => 'item.priceFormula.edit'])->middleware('can:item_categories.update');
        Route::post('{id}/edit', ['uses' => 'ItemPriceFormulaController@update', 'as' => 'item.priceFormula.update'])->middleware('can:item_categories.update');
        Route::get('{id}/delete', ['uses' => 'ItemPriceFormulaController@delete', 'as' => 'item.priceFormula.delete'])->middleware('can:item_categories.delete');
        Route::post('delete/confirmation/modal', ['uses' => 'ItemPriceFormulaController@deleteModal', 'as' => 'item.priceFormula.delete.modal'])->middleware('can:item_categories.delete');
    });
});
