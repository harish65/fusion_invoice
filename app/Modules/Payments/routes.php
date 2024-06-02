<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\Payments\Controllers'], function ()
{
    Route::group(['prefix' => 'payments'], function ()
    {
        Route::get('/', ['uses' => 'PaymentController@index', 'as' => 'payments.index'])->middleware('can:payments.view');
        Route::get('create-payment', ['uses' => 'PaymentController@createPayment', 'as' => 'payments.createPayment'])->middleware('can:payments.create');
        Route::post('store-payment', ['uses' => 'PaymentController@storePayment', 'as' => 'payments.storePayment'])->middleware('can:payments.create');
        Route::get('edit-payment/{payment}', ['uses' => 'PaymentController@editPayment', 'as' => 'payments.editPayment'])->middleware('can:payments.update');
        Route::post('capture-payment-detail', ['uses' => 'PaymentController@capturePaymentDetail', 'as' => 'payments.capturePaymentDetail'])->middleware('can:payments.create');
        Route::get('fetch-invoices-list', ['uses' => 'PaymentController@fetchInvoicesList', 'as' => 'payments.fetchInvoicesList'])->middleware('can:payments.create');
        Route::post('prepare-credit-applications/{creditMemo}', ['uses' => 'PaymentController@prepareCreditApplication', 'as' => 'payments.prepareCreditApplication'])->middleware('can:payments.create');
        Route::post('store-credit-applications', ['uses' => 'PaymentController@storeCreditApplication', 'as' => 'payments.storeCreditApplication'])->middleware('can:payments.create');
        Route::post('prepare-invoice-settlement-with-creditmemo/{invoice}', ['uses' => 'PaymentController@prepareInvoiceSettlementWithCreditMemo', 'as' => 'payments.prepareInvoiceSettlementWithCreditMemo'])->middleware('can:payments.create');
        Route::post('store-invoice-settlement-with-creditmemo', ['uses' => 'PaymentController@storeInvoiceSettlementWithCreditMemo', 'as' => 'payments.storeInvoiceSettlementWithCreditMemo'])->middleware('can:payments.create');
        Route::post('prepare-invoice-settlement-with-prepayment/{invoice}', ['uses' => 'PaymentController@prepareInvoiceSettlementWithPrePayment', 'as' => 'payments.prepareInvoiceSettlementWithPrePayment'])->middleware('can:payments.create');
        Route::post('store-invoice-settlement-with-prepayment', ['uses' => 'PaymentController@storeInvoiceSettlementWithPrePayment', 'as' => 'payments.storeInvoiceSettlementWithPrePayment'])->middleware('can:payments.create');
        Route::post('create', ['uses' => 'PaymentController@create', 'as' => 'payments.create'])->middleware('can:payments.create');
        Route::post('store', ['uses' => 'PaymentController@store', 'as' => 'payments.store'])->middleware('can:payments.create');
        Route::get('{payment}', ['uses' => 'PaymentController@edit', 'as' => 'payments.edit'])->middleware('can:payments.update');
        Route::get('applications/{payment}', ['uses' => 'PaymentController@applications', 'as' => 'payments.applications'])->middleware('can:payments.view');
        Route::post('{payment}', ['uses' => 'PaymentController@update', 'as' => 'payments.update'])->middleware('can:payments.update');
        Route::get('{payment}/delete', ['uses' => 'PaymentController@delete', 'as' => 'payments.delete'])->middleware('can:payments.delete');
        Route::post('bulk/delete', ['uses' => 'PaymentController@bulkDelete', 'as' => 'payments.bulk.delete'])->middleware('can:payments.delete');
        Route::post('custom-field/{id?}/delete-image/{field_name?}', ['uses' => 'PaymentController@deleteImage', 'as' => 'payments.deleteImage'])->middleware('can:payments.update');
        Route::get('{id}/pdf', ['uses' => 'PaymentController@pdf', 'as' => 'payments.pdf'])->middleware('can:payments.view');
        Route::post('delete/modal', ['uses' => 'PaymentController@deleteModal', 'as' => 'payments.delete.modal'])->middleware('can:payments.delete');
        Route::post('confirm/payments/modal', ['uses' => 'PaymentController@confirmPaymentsModal', 'as' => 'confirm.payments.modal'])->middleware('can:payments.delete');
        Route::post('confirm/credit-memo-and-pre-payments/modal', ['uses' => 'PaymentController@creditAndprePaymentsInvoiceModal', 'as' => 'confirm.creditMemo.and.prePayments.invoice.modal'])->middleware('can:payments.delete');
        Route::post('confirm/credit-application/modal', ['uses' => 'PaymentController@creditApplication', 'as' => 'confirm.credit.application.modal'])->middleware('can:payments.update');
        Route::post('delete/bulk-payments/modal', ['uses' => 'PaymentController@bulkDeletePaymentsModal', 'as' => 'bulk.delete.payments.modal'])->middleware('can:payments.delete');

    });

    Route::post('invoice/payment/note/edit', ['uses' => 'PaymentController@editPaymentNote', 'as' => 'payments.note.edit'])->middleware('can:payments.update');
    Route::post('invoice/payment/{payment}/note/update', ['uses' => 'PaymentController@updatePaymentNote', 'as' => 'payments.note.update'])->middleware('can:payments.update');

    Route::group(['prefix' => 'payment_mail'], function ()
    {
        Route::post('create', ['uses' => 'PaymentMailController@create', 'as' => 'paymentMail.create'])->middleware('can:payments.update');
        Route::post('store', ['uses' => 'PaymentMailController@store', 'as' => 'paymentMail.store'])->middleware('can:payments.update');
    });

    Route::group(['prefix' => 'invoice/{invoiceId}/payments'], function ()
    {
        Route::get('edit/{paymentInvoiceId}', ['uses' => 'PaymentController@editInvoicePayment', 'as' => 'invoices.payments.edit'])->middleware('can:payments.update');
        Route::post('edit/{paymentId}', ['uses' => 'PaymentController@updateInvoicePayment', 'as' => 'invoices.payments.update'])->middleware('can:payments.update');
        Route::post('delete', ['uses' => 'PaymentController@deleteInvoicePayment', 'as' => 'invoices.payments.delete'])->middleware('can:payments.delete');
    });
});