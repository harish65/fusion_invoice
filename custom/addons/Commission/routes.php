<?php
/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'Addons\Commission\Controllers'], function ()
{
    Route::group(['prefix' => 'commission'], function ()
    {
        Route::group(['prefix' => 'invoice_commissions'], function ()
        {
            Route::get('/', ['uses' => 'CommissionInvoiceController@index', 'as' => 'invoice.commission.index']);
            Route::get('{id}/load', ['uses' => 'CommissionInvoiceController@load', 'as' => 'invoice.commission.load']);
            Route::get('{id}/create', ['uses' => 'CommissionInvoiceController@create', 'as' => 'invoice.commission.create'])->middleware('can:commission.create');
            Route::post('create', ['uses' => 'CommissionInvoiceController@store', 'as' => 'invoice.commission.store'])->middleware('can:commission.create');
            Route::get('{id}/{invoice_id}/edit', ['uses' => 'CommissionInvoiceController@edit', 'as' => 'invoice.commission.edit'])->middleware('can:commission.update');
            Route::post('{id}/update', ['uses' => 'CommissionInvoiceController@update', 'as' => 'invoice.commission.update'])->middleware('can:commission.update');
            Route::get('{id}/delete', ['uses' => 'CommissionInvoiceController@delete', 'as' => 'invoice.commission.delete'])->middleware('can:commission.delete');
            Route::post('bulk/delete', ['uses' => 'CommissionInvoiceController@bulkDelete', 'as' => 'invoices.commission.bulk.delete'])->middleware('can:commission.delete');
            Route::post('bulk/status', ['uses' => 'CommissionInvoiceController@bulkStatus', 'as' => 'invoices.commission.bulk.status'])->middleware('can:commission.update');
        });

        Route::group(['prefix' => 'recurring_commissions'], function ()
        {
            Route::get('/', ['uses' => 'CommissionRecurringInvoiceController@index', 'as' => 'recurring.invoice.commission.index']);
            Route::get('{id}/load', ['uses' => 'CommissionRecurringInvoiceController@load', 'as' => 'recurring.invoice.commission.load']);
            Route::get('{id}/create', ['uses' => 'CommissionRecurringInvoiceController@create', 'as' => 'recurring.invoice.commission.create'])->middleware('can:commission.create');
            Route::post('create', ['uses' => 'CommissionRecurringInvoiceController@store', 'as' => 'recurring.invoice.commission.store'])->middleware('can:commission.create');
            Route::get('{id}/{invoice_id}/edit', ['uses' => 'CommissionRecurringInvoiceController@edit', 'as' => 'recurring.invoice.commission.edit'])->middleware('can:commission.update');
            Route::post('{id}/update', ['uses' => 'CommissionRecurringInvoiceController@update', 'as' => 'recurring.invoice.commission.update'])->middleware('can:commission.update');
            Route::get('{id}/delete', ['uses' => 'CommissionRecurringInvoiceController@delete', 'as' => 'recurring.invoice.commission.delete'])->middleware('can:commission.delete');
            Route::post('bulk/delete', ['uses' => 'CommissionRecurringInvoiceController@bulkDelete', 'as' => 'recurring.invoices.commission.bulk.delete'])->middleware('can:commission.delete');
        });

        Route::group(['prefix' => 'type'], function ()
        {
            Route::get('/', ['uses' => 'CommissionTypeController@index', 'as' => 'invoice.commission.type.index']);
            Route::get('create', ['uses' => 'CommissionTypeController@create', 'as' => 'invoice.commission.type.create'])->middleware('can:commission.create');
            Route::post('create', ['uses' => 'CommissionTypeController@store', 'as' => 'invoice.commission.type.store'])->middleware('can:commission.create');
            Route::get('{id}/edit', ['uses' => 'CommissionTypeController@edit', 'as' => 'invoice.commission.type.edit'])->middleware('can:commission.update');
            Route::post('{id}/update', ['uses' => 'CommissionTypeController@update', 'as' => 'invoice.commission.type.update'])->middleware('can:commission.update');
            Route::get('{id}/delete', ['uses' => 'CommissionTypeController@delete', 'as' => 'invoice.commission.type.delete'])->middleware('can:commission.delete');
            Route::post('commission/types', ['uses' => 'CommissionTypeController@commissionTypes', 'as' => 'invoice.commission.type.commissiontypes'])->middleware('can:commission.update');
            Route::post('delete/confirmation/modal', ['uses' => 'CommissionTypeController@deleteModal', 'as' => 'invoice.commission.type.delete.modal'])->middleware('can:commission.delete');

        });
    });

    Route::group(['prefix' => 'report'], function ()
    {
        Route::get('commission', ['uses' => 'CommissionReportController@index', 'as' => 'invoice.commission.reports.index']);
        Route::get('commission/html', ['uses' => 'CommissionReportController@html', 'as' => 'invoice.commission.reports.html'])->middleware('can:commission.view');
        Route::get('commission/pdf', ['uses' => 'CommissionReportController@pdf', 'as' => 'invoice.commission.reports.pdf'])->middleware('can:commission.view');
        Route::get('commission/csv', ['uses' => 'CommissionReportController@csv', 'as' => 'invoice.commission.reports.csv'])->middleware('can:commission.view');
    });
});
