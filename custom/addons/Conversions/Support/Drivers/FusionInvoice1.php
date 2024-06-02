<?php

namespace Addons\Conversions\Support\Drivers;

use Addons\Conversions\Support\AbstractConverter;
use Illuminate\Support\Facades\DB;

class FusionInvoice1 extends AbstractConverter
{
    public function getClients()
    {
        return DB::connection('FusionInvoice1')->
        table('clients')
            ->select(
                'client_name AS name',
                DB::raw("CASE WHEN client_address_2 <> '' THEN CONCAT(client_address_1,char(13),client_address_2) ELSE client_address_1 END AS address"),
                'client_city AS city',
                'client_state AS state',
                'client_zip AS zip',
                'client_country AS country',
                'client_phone AS phone',
                'client_fax AS fax',
                'client_mobile AS mobile',
                'client_email AS email',
                'client_web AS web',
                'client_custom.*'
            )
            ->leftJoin('client_custom', 'client_custom.client_id', '=', 'clients.client_id')
            ->get();
    }

    public function getQuotes()
    {
        return DB::connection('FusionInvoice1')
            ->table('quotes')
            ->select(
                'quote_date_created AS quote_date',
                DB::raw("'Company Profile' AS company_profile"),
                'client_name',
                'quote_number AS number',
                DB::raw("'Quote Default' AS document_number_scheme_id"),
                DB::raw("CASE quote_status_id WHEN 1 THEN 1 WHEN 2 THEN 2 WHEN 4 THEN 3 WHEN 5 THEN 4 WHEN 6 THEN 5 END AS quote_status_id"),
                'quote_date_expires AS expires_at'
            )
            ->join('clients', 'clients.client_id', '=', 'quotes.client_id')
            ->get();
    }

    /*
     * 1 = 1 (Draft)
     * 2 = 2 (Sent)
     * 4 = 3 (Approved)
     * 5 = 4 (Rejected)
     * 6 = 5 (Canceled)
     */

    public function getQuoteItems()
    {
        return DB::connection('FusionInvoice1')
            ->table('quote_items')
            ->select(
                'quote_number AS quote_id',
                'item_name AS name',
                'item_quantity AS quantity',
                'item_price AS price',
                'item_description AS description',
                'quote_level_tax_rates.tax_rate_name AS quote_level_tax_rate',
                'item_level_tax_rates.tax_rate_name AS item_level_tax_rate',
                DB::raw("IFNULL(item_level_tax_rates.tax_rate_name, quote_level_tax_rates.tax_rate_name) AS tax_rate_id"),
                DB::raw("CASE WHEN item_level_tax_rates.tax_rate_name IS NOT NULL THEN quote_level_tax_rates.tax_rate_name END AS tax_rate_2_id")
            )
            ->join('quotes', 'quotes.quote_id', '=', 'quote_items.quote_id')
            ->leftJoin('quote_tax_rates', 'quote_tax_rates.quote_id', '=', 'quotes.quote_id')
            ->leftJoin('tax_rates AS item_level_tax_rates', 'item_level_tax_rates.tax_rate_id', '=', 'quote_items.item_tax_rate_id')
            ->leftJoin('tax_rates AS quote_level_tax_rates', 'quote_level_tax_rates.tax_rate_id', '=', 'quote_tax_rates.tax_rate_id')
            ->get();
    }

    public function getInvoices()
    {
        return DB::connection('FusionInvoice1')
            ->table('invoices')
            ->select(
                'invoice_date_created AS invoice_date',
                DB::raw("'Company Profile' AS company_profile"),
                'client_name',
                'invoice_number AS number',
                DB::raw("'Invoice Default' AS document_number_scheme_id"),
                DB::raw("CASE invoice_status_id WHEN 1 THEN 1 WHEN 2 THEN 2 END AS invoice_status_id"),
                'invoice_date_due AS due_at',
                'invoice_terms AS terms'
            )
            ->join('clients', 'clients.client_id', '=', 'invoices.client_id')
            ->get();
    }

    /*
     * 1 = 1 (Draft)
     * 2 = 2 (Sent)
     */

    public function getInvoiceItems()
    {
        return DB::connection('FusionInvoice1')
            ->table('invoice_items')
            ->select(
                'invoice_number AS invoice_id',
                'item_name AS name',
                'item_quantity AS quantity',
                'item_price AS price',
                'item_description AS description',
                'invoice_level_tax_rates.tax_rate_name AS invoice_level_tax_rate',
                DB::raw("IFNULL(item_level_tax_rates.tax_rate_name, invoice_level_tax_rates.tax_rate_name) AS tax_rate_id"),
                DB::raw("CASE WHEN item_level_tax_rates.tax_rate_name IS NOT NULL THEN invoice_level_tax_rates.tax_rate_name END AS tax_rate_2_id")
            )
            ->join('invoices', 'invoices.invoice_id', '=', 'invoice_items.invoice_id')
            ->leftJoin('invoice_tax_rates', 'invoice_tax_rates.invoice_id', '=', 'invoices.invoice_id')
            ->leftJoin('tax_rates AS item_level_tax_rates', 'item_level_tax_rates.tax_rate_id', '=', 'invoice_items.item_tax_rate_id')
            ->leftJoin('tax_rates AS invoice_level_tax_rates', 'invoice_level_tax_rates.tax_rate_id', '=', 'invoice_tax_rates.tax_rate_id')
            ->get();
    }

    public function getPayments()
    {
        return DB::connection('FusionInvoice1')
            ->table('payments')
            ->select(
                'payment_date AS paid_at',
                'invoice_number AS invoice_id',
                'payment_amount AS amount',
                'payment_method_name AS payment_method_id',
                'payment_note AS note'
            )
            ->join('invoices', 'invoices.invoice_id', '=', 'payments.invoice_id')
            ->leftJoin('payment_methods', 'payment_methods.payment_method_id', '=', 'payments.payment_method_id')
            ->get();
    }

    public function getInvoiceComparison()
    {
        $invoices = [];

        $oldInvoices = DB::connection('FusionInvoice1')
            ->table('invoices')
            ->select('invoices.invoice_number', 'invoice_amounts.invoice_total', 'invoice_amounts.invoice_paid', 'invoice_amounts.invoice_balance')
            ->join('invoice_amounts', 'invoice_amounts.invoice_id', '=', 'invoices.invoice_id')
            ->get();

        $newInvoices = DB::table('invoices')
            ->select('invoices.number', 'invoice_amounts.total', 'invoice_amounts.paid', 'invoice_amounts.balance')
            ->join('invoice_amounts', 'invoice_amounts.invoice_id', '=', 'invoices.id')
            ->get();

        foreach ($oldInvoices as $invoice)
        {
            $invoices[$invoice->invoice_number] = [
                'number'       => $invoice->invoice_number,
                'prev_total'   => round($invoice->invoice_total, 2),
                'prev_paid'    => round($invoice->invoice_paid, 2),
                'prev_balance' => round($invoice->invoice_balance, 2),
            ];
        }

        foreach ($newInvoices as $invoice)
        {
            $invoices[$invoice->number]['total']   = round($invoice->total, 2);
            $invoices[$invoice->number]['paid']    = round($invoice->paid, 2);
            $invoices[$invoice->number]['balance'] = round($invoice->balance, 2);
            $invoices[$invoice->number]['difference'] = round($invoice->balance - $invoices[$invoice->number]['prev_balance'], 2);
        }

        foreach ($invoices as $key => $invoice)
        {
            if ($invoice['prev_total'] == $invoice['total'] and $invoice['prev_paid'] == $invoice['paid'] and $invoice['prev_balance'] == $invoice['balance'])
            {
                unset($invoices[$key]);
            }
        }

        return $invoices;
    }

    public function getQuoteComparison()
    {
        $quotes = [];

        $oldQuotes = DB::connection('FusionInvoice1')
            ->table('quotes')
            ->select('quotes.quote_number', 'quote_amounts.quote_total')
            ->join('quote_amounts', 'quote_amounts.quote_id', '=', 'quotes.quote_id')
            ->get();

        $newQuotes = DB::table('quotes')
            ->select('quotes.number', 'quote_amounts.total')
            ->join('quote_amounts', 'quote_amounts.quote_id', '=', 'quotes.id')
            ->get();

        foreach ($oldQuotes as $quote)
        {
            $quotes[$quote->quote_number] = [
                'number'     => $quote->quote_number,
                'prev_total' => round($quote->quote_total, 2),
            ];
        }

        foreach ($newQuotes as $quote)
        {
            $quotes[$quote->number]['total'] = round($quote->total, 2);
        }

        foreach ($quotes as $key => $quote)
        {
            if (isset($quote['prev_total']) and $quote['prev_total'] == $quote['total'])
            {
                unset($quotes[$key]);
            }
        }

        return $quotes;
    }

    public function getPaymentComparison()
    {

    }
}