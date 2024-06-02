<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['prefix' => 'custom_fields', 'middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\CustomFields\Controllers'], function ()
{
        Route::get('/', ['uses' => 'CustomFieldController@index', 'as' => 'customFields.index'])->middleware('can:custom_fields.view');
        Route::get('create', ['uses' => 'CustomFieldController@create', 'as' => 'customFields.create'])->middleware('can:custom_fields.create');
        Route::get('{id}/edit', ['uses' => 'CustomFieldController@edit', 'as' => 'customFields.edit'])->middleware('can:custom_fields.update');
        Route::get('{id}/delete', ['uses' => 'CustomFieldController@delete', 'as' => 'customFields.delete'])->middleware('can:custom_fields.delete');
        Route::post('bulk/delete', ['uses' => 'CustomFieldController@bulkDelete', 'as' => 'customFields.bulk.delete'])->middleware('can:custom_fields.delete');

        Route::post('store', ['uses' => 'CustomFieldController@store', 'as' => 'customFields.store'])->middleware('can:custom_fields.create');
        Route::post('update/{id}', ['uses' => 'CustomFieldController@update', 'as' => 'customFields.update'])->middleware('can:custom_fields.update');
        Route::post('reorder/store', ['uses' => 'CustomFieldController@reorder', 'as' => 'customFields.reorder'])->middleware('can:custom_fields.update');

        Route::post('delete/modal', ['uses' => 'CustomFieldController@deleteModal', 'as' => 'customFields.delete.modal'])->middleware('can:custom_fields.delete');
        Route::post('delete/bulk-custom-filed/modal', ['uses' => 'CustomFieldController@deleteBulkCustomFieldsModal', 'as' => 'bulk.customFields.delete.modal'])->middleware('can:custom_fields.delete');
});