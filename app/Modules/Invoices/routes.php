<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\Invoices\Controllers'], function ()
{
    Route::group(['prefix' => 'invoices'], function ()
    {
        Route::get('/', ['uses' => 'InvoiceController@index', 'as' => 'invoices.index'])->middleware('can:invoices.view');
        Route::get('ajax/filter-tags', ['uses' => 'InvoiceController@showFilterTags', 'as' => 'invoice.filterTags'])->middleware('can:invoices.view');
        Route::get('create', ['uses' => 'InvoiceCreateController@create', 'as' => 'invoices.create'])->middleware('can:invoices.create');
        Route::post('create', ['uses' => 'InvoiceCreateController@store', 'as' => 'invoices.store'])->middleware('can:invoices.create');
        Route::get('{id}/edit', ['uses' => 'InvoiceEditController@edit', 'as' => 'invoices.edit'])->middleware('can:invoices.update');
        Route::get('{id}/pdf', ['uses' => 'InvoiceController@pdf', 'as' => 'invoices.pdf'])->middleware('can:invoices.view');
        Route::get('{id}/save-pdf', ['uses' => 'InvoiceController@savePdf', 'as' => 'invoices.save.pdf'])->middleware('can:invoices.view');
        Route::get('{id}/print', ['uses' => 'InvoiceController@printPdf', 'as' => 'invoices.print'])->middleware('can:invoices.view');
        Route::get('{id}/edit/refresh', ['uses' => 'InvoiceEditController@refreshEdit', 'as' => 'invoiceEdit.refreshEdit'])->middleware('can:invoices.update');
        Route::post('edit/refresh_to', ['uses' => 'InvoiceEditController@refreshTo', 'as' => 'invoiceEdit.refreshTo'])->middleware('can:invoices.update');
        Route::post('edit/refresh_from', ['uses' => 'InvoiceEditController@refreshFrom', 'as' => 'invoiceEdit.refreshFrom'])->middleware('can:invoices.update');
        Route::post('edit/refresh_totals', ['uses' => 'InvoiceEditController@refreshTotals', 'as' => 'invoiceEdit.refreshTotals'])->middleware('can:invoices.update');
        Route::post('recalculate', ['uses' => 'InvoiceRecalculateController@recalculate', 'as' => 'invoices.recalculate'])->middleware('can:invoices.update');
        Route::post('bulk/delete', ['uses' => 'InvoiceController@bulkDelete', 'as' => 'invoices.bulk.delete'])->middleware('can:invoices.delete');
        Route::post('bulk/status', ['uses' => 'InvoiceController@bulkStatus', 'as' => 'invoices.bulk.status'])->middleware('can:invoices.update');
        Route::get('bulk/download/pdf', ['uses' => 'InvoiceController@bulkPdf', 'as' => 'invoices.bulk.pdf'])->middleware('can:invoices.view');
        Route::get('bulk/save/pdf', ['uses' => 'InvoiceController@saveBulkPdf', 'as' => 'invoices.bulk.save.pdf'])->middleware('can:invoices.view');
        Route::get('{files}/bulk/print', ['uses' => 'InvoiceController@printBulkPdf', 'as' => 'invoices.bulk.print'])->middleware('can:invoices.view');
        Route::post('custom_field/{id?}/delete_image/{field_name?}', ['uses' => 'InvoiceEditController@deleteImage', 'as' => 'invoiceEdit.deleteImage'])->middleware('can:invoices.update');
        Route::post('add/line/item', ['uses' => 'InvoiceEditController@addLineItem', 'as' => 'invoice.add.new.lineItem'])->middleware('can:invoices.create');
        Route::post('{id}/mail', ['uses' => 'InvoiceController@mailed', 'as' => 'invoices.save.dateMailed'])->middleware('can:invoices.update');
        Route::post('{id}/empty-invoice-delete', ['uses' => 'InvoiceController@emptyInvoiceDelete', 'as' => 'invoices.empty.invoice.delete'])->middleware('can:invoices.delete');
        Route::post('{id}/un_mail', ['uses' => 'InvoiceController@unMailed', 'as' => 'invoices.remove.dateMailed'])->middleware('can:invoices.update');
        Route::get('{id}/print-pdf-and-mark-as-mailed', ['uses' => 'InvoiceController@printPdfAndMarkAsMailed', 'as' => 'invoices.print.pdf.and.mark.as.mailed'])->middleware('can:invoices.view');
        Route::post('client/create', ['uses' => 'InvoiceController@clientCreate', 'as' => 'invoices.client.create.modal'])->middleware('can:clients.create');
        Route::post('delete/modal', ['uses' => 'InvoiceController@deleteModal', 'as' => 'invoices.delete.modal'])->middleware('can:invoices.delete');
        Route::post('delete/invoice-commission/modal', ['uses' => 'InvoiceController@deleteInvoiceCommissionModal', 'as' => 'invoices.commission.delete.modal'])->middleware('can:invoices.delete');
        Route::post('delete/item/modal', ['uses' => 'InvoiceController@deleteItemModal', 'as' => 'invoices.item.delete.modal'])->middleware('can:invoices.delete');
        Route::post('delete/payment/modal', ['uses' => 'InvoiceController@deletePaymentModal', 'as' => 'invoices.payment.delete.modal'])->middleware('can:invoices.delete');
        Route::post('delete/bulk-invoice/modal', ['uses' => 'InvoiceController@bulkDeleteInvoicesModal', 'as' => 'bulk.delete.invoices.modal'])->middleware('can:invoices.delete');
        Route::post('delete/credit-memo-application/modal', ['uses' => 'InvoiceController@deleteCreditMemoApplicationModal', 'as' => 'invoices.delete.credit.memo.application.modal'])->middleware('can:invoices.delete');
        Route::post('status-change/bulk-invoice/modal', ['uses' => 'InvoiceController@bulkStatusChangeInvoicesModal', 'as' => 'bulk.status.change.invoices.modal'])->middleware('can:invoices.update');

        Route::post('allow-editing-invoices-in-status', ['uses' => 'InvoiceController@AllowEditingInvoicesInStatus', 'as' => 'allow.editing.invoices.in.status'])->middleware('can:invoices.update');

        Route::post('{id}/update-summary-and-tag', ['uses' => 'InvoiceEditController@updateSummaryAndTags', 'as' => 'invoices.update.summary.and.tags'])->middleware('can:invoices.update');

        Route::group(['middleware' => ['check.invoiceStatus']], function ()
        {
            Route::post('{id}/edit', ['uses' => 'InvoiceEditController@update', 'as' => 'invoices.update'])->middleware('can:invoices.update');
            Route::get('{id}/delete', ['uses' => 'InvoiceController@delete', 'as' => 'invoices.delete'])->middleware('can:invoices.delete');
            Route::post('edit/update_client', ['uses' => 'InvoiceEditController@updateClient', 'as' => 'invoiceEdit.updateClient'])->middleware('can:invoices.update');
            Route::post('edit/update_company_profile', ['uses' => 'InvoiceEditController@updateCompanyProfile', 'as' => 'invoiceEdit.updateCompanyProfile'])->middleware('can:invoices.update');
            Route::post('{id}/status-change', ['uses' => 'InvoiceController@statusChangeToDraft', 'as' => 'invoices.status.changeToDraft'])->middleware('can:invoices.update');
            Route::post('{id}/status/cancel', ['uses' => 'InvoiceController@statusChangeToCancel', 'as' => 'invoices.status.changeToCancel'])->middleware('can:invoices.update');
        });

    });

    Route::group(['prefix' => 'invoice_setting'], function ()
    {
        Route::get('get/filter-columns', ['uses' => 'InvoiceController@showFilterColumns', 'as' => 'invoice.get.filterColumns'])->middleware('can:invoices.update');
        Route::post('store/filter-columns', ['uses' => 'InvoiceController@storeInvoiceListingColumnSettings', 'as' => 'invoice.store.filterColumnSetting'])->middleware('can:invoices.update');
    });

    Route::group(['prefix' => 'invoice_copy', 'middleware' => ['check.invoiceStatus']], function ()
    {
        Route::post('create', ['uses' => 'InvoiceCopyController@create', 'as' => 'invoiceCopy.create'])->middleware('can:invoices.create');
        Route::post('store', ['uses' => 'InvoiceCopyController@store', 'as' => 'invoiceCopy.store'])->middleware('can:invoices.create');
    });

    Route::group(['prefix' => 'invoice_timeline'], function ()
    {
        Route::post('show', ['uses' => 'InvoiceController@showTimeLine', 'as' => 'invoice.showTimeLine'])->middleware('can:invoices.view');
    });

    Route::group(['prefix' => 'invoice_to_recurring_invoice_copy', 'middleware' => ['check.invoiceStatus']], function ()
    {
        Route::post('create', ['uses' => 'InvoiceToRecurringInvoiceCopyController@create', 'as' => 'invoiceToRecurringInvoiceCopy.create'])->middleware('can:recurring_invoices.create');
        Route::post('store', ['uses' => 'InvoiceToRecurringInvoiceCopyController@store', 'as' => 'invoiceToRecurringInvoiceCopy.store'])->middleware('can:recurring_invoices.create');
    });

    Route::group(['prefix' => 'invoice_mail'], function ()
    {
        Route::group(['prefix' => 'invoice_mail'], function ()
        {
            Route::post('create', ['uses' => 'InvoiceMailController@create', 'as' => 'invoiceMail.create'])->middleware('can:invoices.update');
            Route::post('store', ['uses' => 'InvoiceMailController@store', 'as' => 'invoiceMail.store'])->middleware('can:invoices.update');
            Route::get('payment-reminder/{id}', ['uses' => 'InvoiceMailController@sendPaymentDueReminder', 'as' => 'invoices.payment-reminder'])->middleware('can:invoices.create');
            Route::get('payment-notice/{id}', ['uses' => 'InvoiceMailController@sendPaymentUpcomingNotice', 'as' => 'invoices.payment-notice'])->middleware('can:invoices.create');
        });

        Route::group(['prefix' => 'invoice_item', 'middleware' => ['check.invoiceStatus']], function ()
        {
            Route::post('delete', ['uses' => 'InvoiceItemController@delete', 'as' => 'invoiceItem.delete'])->middleware('can:invoices.update');
        });

    });
});