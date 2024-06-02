<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Support;

use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\CustomFields\Support\CustomFieldsParser;
use FI\Modules\Invoices\Events\InvoiceHTMLCreating;
use FI\Modules\Quotes\Events\QuoteHTMLCreating;
use Illuminate\Support\Facades\Log;
use Throwable;

class HTML
{
    public static function invoice($invoice)
    {
        app()->setLocale($invoice->client->language);

        config(['fi.baseCurrency' => $invoice->currency_code]);

        event(new InvoiceHTMLCreating($invoice));

        $template = str_replace('.blade.php', '', $invoice->template);

        if (view()->exists('invoice_templates.' . $template))
        {
            $template = 'invoice_templates.' . $template;
        }
        else
        {
            $template = 'templates.invoices.default';
        }

        try
        {
            return view($template)
                ->with('invoice', $invoice)
                ->with('invoiceItemCustomFields', CustomFieldsParser::getFields('invoice_items'))
                ->with('logo', $invoice->companyProfile->logo())
                ->with('hasLineItemDiscount', $invoice->hasLineItemDiscount())
                ->render();
        }
        catch (Throwable $e)
        {
            Log::error($e->getMessage());
        }
    }

    public static function quote($quote)
    {
        app()->setLocale($quote->client->language);

        config(['fi.baseCurrency' => $quote->currency_code]);

        event(new QuoteHTMLCreating($quote));

        $template = str_replace('.blade.php', '', $quote->template);

        if (view()->exists('quote_templates.' . $template))
        {
            $template = 'quote_templates.' . $template;
        }
        else
        {
            $template = 'templates.quotes.default';
        }

        try
        {
            return view($template)
                ->with('quote', $quote)
                ->with('logo', $quote->companyProfile->logo())->render();
        }
        catch (Throwable $e)
        {
            Log::error($e->getMessage());
        }
    }

    public static function payment($payment)
    {
        if (isset($payment->paymentInvoice[0]->invoice->client->language))
        {
            app()->setLocale($payment->paymentInvoice[0]->invoice->client->language);
            config(['fi.baseCurrency' => $payment->paymentInvoice[0]->invoice->currency_code]);
            $company_profile      = $payment->paymentInvoice[0]->invoice->companyProfile;
            $payment_invoice_text = trans('fi.payment_invoices_text', ['amount' => $payment->formatted_amount]);
        }
        else
        {
            $company_profile      = CompanyProfile::whereIsDefault(1)->first();
            $payment_invoice_text = trans('fi.payment_text', ['amount' => $payment->formatted_amount]);
        }

        try
        {
            return view('templates.payments.default')
                ->with('payment', $payment)
                ->with('companyProfile', $company_profile)
                ->with('paymentText', $payment_invoice_text)->render();
        }
        catch (Throwable $e)
        {
            Log::error($e->getMessage());
        }
    }

}