<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['prefix' => 'mail_log', 'middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\MailQueue\Controllers'], function ()
{
    Route::get('/', ['uses' => 'MailLogController@index', 'as' => 'mailLog.index'])->middleware('can:mail_queue.view');
    Route::post('content', ['uses' => 'MailLogController@content', 'as' => 'mailLog.content'])->middleware('can:mail_queue.view');
    Route::get('{id}/delete', ['uses' => 'MailLogController@delete', 'as' => 'mailLog.delete'])->middleware('can:mail_queue.delete');
    Route::post('delete/modal', ['uses' => 'MailLogController@deleteModal', 'as' => 'mailLog.delete.modal'])->middleware('can:mail_queue.delete');
});