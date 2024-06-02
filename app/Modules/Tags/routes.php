<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['prefix' => 'tags', 'middleware' => ['web', 'auth'], 'namespace' => 'FI\Modules\Tags\Controllers'], function ()
{
    Route::post('delete', ['uses' => 'TagController@delete', 'as' => 'tags.delete'])->middleware('can:tags.delete');
    Route::post('modal-edit', ['uses' => 'TagController@editModal', 'as' => 'tags.edit.modal']);
    Route::post('modal-update', ['uses' => 'TagController@updateModal', 'as' => 'tags.update.modal']);
    Route::post('category-wise', ['uses' => 'TagController@categoryWiseData', 'as' => 'tags.category.wise.data']);
});