<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['prefix' => 'client_center', 'middleware' => 'web', 'namespace' => 'FI\Modules\ClientCenter\Controllers'], function ()
{
    Route::get('/', ['uses' => 'ClientCenterDashboardController@redirectToLogin']);
    Route::get('/public/invoice/{invoiceKey}/{token?}', ['uses' => 'ClientCenterPublicInvoiceController@show', 'as' => 'clientCenter.public.invoice.show']);
    Route::get('invoice/{invoiceKey}/pdf', ['uses' => 'ClientCenterPublicInvoiceController@pdf', 'as' => 'clientCenter.public.invoice.pdf']);
    Route::get('invoice/{invoiceKey}/html', ['uses' => 'ClientCenterPublicInvoiceController@html', 'as' => 'clientCenter.public.invoice.html']);

    Route::group(['prefix' => 'quote'], function ()
    {
        Route::get('public/{quoteKey}/{token?}', ['uses' => 'ClientCenterPublicQuoteController@show', 'as' => 'clientCenter.public.quote.show']);
        Route::get('{quoteKey}/pdf', ['uses' => 'ClientCenterPublicQuoteController@pdf', 'as' => 'clientCenter.public.quote.pdf']);
        Route::get('{quoteKey}/html', ['uses' => 'ClientCenterPublicQuoteController@html', 'as' => 'clientCenter.public.quote.html']);
        Route::get('{quoteKey}/approve/{token?}', ['uses' => 'ClientCenterPublicQuoteController@approve', 'as' => 'clientCenter.public.quote.approve']);
        Route::get('{quoteKey}/reject/{token?}', ['uses' => 'ClientCenterPublicQuoteController@reject', 'as' => 'clientCenter.public.quote.reject']);
        Route::post('approve-reject-modal', ['uses' => 'ClientCenterPublicQuoteController@approveAndRejectModal', 'as' => 'clientCenter.public.quote.approve.and.reject.modal']);
    });

    Route::group(['middleware' => 'auth.clientCenter'], function ()
    {
        Route::get('dashboard', ['uses' => 'ClientCenterDashboardController@index', 'as' => 'clientCenter.dashboard']);
        Route::get('invoices', ['uses' => 'ClientCenterInvoiceController@index', 'as' => 'clientCenter.invoices']);
        Route::get('quotes', ['uses' => 'ClientCenterQuoteController@index', 'as' => 'clientCenter.quotes']);
        Route::get('payments', ['uses' => 'ClientCenterPaymentController@index', 'as' => 'clientCenter.payments']);
        Route::get('attachments', ['uses' => 'ClientCenterAttachmentController@index', 'as' => 'clientCenter.attachments']);
    });
});