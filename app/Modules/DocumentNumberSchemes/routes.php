<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\DocumentNumberSchemes\Controllers'], function ()
{
    Route::group(['prefix' => 'document_number_schemes'], function ()
    {
        Route::get('/', ['uses' => 'DocumentNumberSchemeController@index', 'as' => 'documentNumberSchemes.index'])->middleware('can:document_number_schemes.view');
        Route::get('create', ['uses' => 'DocumentNumberSchemeController@create', 'as' => 'documentNumberSchemes.create'])->middleware('can:document_number_schemes.create');
        Route::get('{document_number_scheme}/edit', ['uses' => 'DocumentNumberSchemeController@edit', 'as' => 'documentNumberSchemes.edit'])->middleware('can:document_number_schemes.update');
        Route::get('{document_number_scheme}/delete', ['uses' => 'DocumentNumberSchemeController@delete', 'as' => 'documentNumberSchemes.delete'])->middleware('can:document_number_schemes.delete');
        Route::post('store/document_number_schemes', ['uses' => 'DocumentNumberSchemeController@store', 'as' => 'documentNumberSchemes.store'])->middleware('can:document_number_schemes.create');
        Route::post('update/{document_number_scheme}', ['uses' => 'DocumentNumberSchemeController@update', 'as' => 'documentNumberSchemes.update'])->middleware('can:document_number_schemes.update');
        Route::post('delete/confirmation/modal', ['uses' => 'DocumentNumberSchemeController@deleteModal', 'as' => 'document.number.schemes.delete.modal'])->middleware('can:document_number_schemes.delete');
    });
});