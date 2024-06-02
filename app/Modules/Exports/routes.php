<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'prefix' => 'export', 'namespace' => 'FI\Modules\Exports\Controllers'], function ()
{
    Route::get('/', ['uses' => 'ExportController@index', 'as' => 'export.index'])->middleware('can:exports.view');
    Route::get('/populate-form/{type}', ['uses' => 'ExportController@populateForm', 'as' => 'export.populate_form'])->middleware('can:exports.view');
    Route::post('save-mapping', ['uses' => 'ExportController@saveMapping', 'as' => 'export.save_mapping'])->middleware('can:exports.view');
    Route::get('change-mapping', ['uses' => 'ExportController@changeMapping', 'as' => 'export.change_mapping'])->middleware('can:exports.view');
    Route::get('delete-mapping/{id}/{type}', ['uses' => 'ExportController@deleteMapping', 'as' => 'export.delete_mapping'])->middleware('can:exports.view');
    Route::post('{export}', ['uses' => 'ExportController@export', 'as' => 'export.export'])->middleware('can:exports.view');
    Route::post('delete/modal', ['uses' => 'ExportController@deleteModal', 'as' => 'export.delete.modal'])->middleware('can:exports.delete');
});