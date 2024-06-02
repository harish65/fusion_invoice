<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'prefix' => 'import', 'namespace' => 'FI\Modules\Import\Controllers'], function ()
{
    Route::get('/', ['uses' => 'ImportController@index', 'as' => 'import.index'])->middleware('can:exports.view');
    Route::get('map/{import_type}', ['uses' => 'ImportController@mapImport', 'as' => 'import.map'])->middleware('can:exports.create');
    Route::get('example/{import_type}', ['uses' => 'ImportController@exampleImport', 'as' => 'import.example'])->middleware('can:exports.view');
    Route::post('save-mapping', ['uses' => 'ImportController@saveMapping', 'as' => 'import.save_mapping'])->middleware('can:exports.create');
    Route::get('change-mapping', ['uses' => 'ImportController@changeMapping', 'as' => 'import.change_mapping'])->middleware('can:exports.create');
    Route::get('delete-mapping/{id}/{type}', ['uses' => 'ImportController@deleteMapping', 'as' => 'import.delete_mapping'])->middleware('can:exports.create');
    Route::post('upload', ['uses' => 'ImportController@upload', 'as' => 'import.upload'])->middleware('can:exports.create');
    Route::post('map-submit/{import_type}', ['uses' => 'ImportController@mapImportSubmit', 'as' => 'import.map.submit'])->middleware('can:exports.create');
    Route::post('delete/modal', ['uses' => 'ImportController@deleteModal', 'as' => 'import.delete.modal'])->middleware('can:exports.delete');
});