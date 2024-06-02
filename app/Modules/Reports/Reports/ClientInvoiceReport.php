<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Reports\Reports;

use FI\Modules\Clients\Models\Client;
use FI\Support\CurrencyFormatter;
use FI\Support\DateFormatter;
use FI\Modules\Currencies\Models\Currency;
use FI\Support\NumberFormatter;

class ClientInvoiceReport
{
    public function getResults($clientId, $fromDate, $toDate, $companyProfileId = null, $invoiceStatus = null, $invoiceTags = null, $includeLineItemDetail = 0)
    {

        if ($clientId == 'null' || $clientId == '')
        {
            $clients = Client::all();
        }
        else
        {
            $clients = Client::whereIn('id', explode(',', $clientId))->get();
        }

        $clientData = [];
        foreach ($clients as $client)
        {

            $invoices = $client->invoices()->select('invoices.*')
                ->join('invoice_amounts', 'invoices.id', '=', 'invoice_amounts.invoice_id')
                ->with('items', 'client.currency', 'amount.invoice.currency')
                ->where('invoice_date', '>=', $fromDate)
                ->where('invoice_date', '<=', $toDate)
                ->orderBy('invoice_date');

            if ($companyProfileId)
            {
                $invoices->where('company_profile_id', $companyProfileId);
            }
            if ($invoiceStatus)
            {
                $invoices->status($invoiceStatus);
            }
            if ($invoiceTags)
            {
                $invoices->whereHas('tags', function ($query) use ($invoiceTags)
                {
                    $query->whereHas('tag', function ($query) use ($invoiceTags)
                    {
                        $query->whereIn('name', explode(",", $invoiceTags));
                    });
                });
            }
            $invoices = $invoices->get();

            $results       = [
                'client_name' => '',
                'from_date'   => '',
                'to_date'     => '',
                'total'       => [],
                'paid'        => [],
                'balance'     => [],
                'records'     => [],
            ];
            $currencyCodes = [];

            foreach ($invoices as $invoice)
            {
                $results['total'][$invoice->currency_code]   = 0;
                $results['paid'][$invoice->currency_code]    = 0;
                $results['balance'][$invoice->currency_code] = 0;
            }

            foreach ($invoices as $invoiceIndex => $invoice)
            {
                if ($invoice->amount)
                {
                    $currencyCodes[$invoice->currency_code] = $invoice->currency_code;

                    $results['records'][$invoice->currency_code][$invoiceIndex] = [
                        'invoice_id'             => $invoice->id,
                        'formatted_invoice_date' => $invoice->formatted_invoice_date,
                        'number'                 => $invoice->number,
                        'total'                  => $invoice->amount->total,
                        'paid'                   => $invoice->amount->paid,
                        'balance'                => $invoice->amount->balance,
                        'formatted_total'        => $invoice->amount->formatted_total,
                        'formatted_paid'         => $invoice->amount->formatted_paid,
                        'formatted_balance'      => $invoice->amount->formatted_balance,
                        'type'                   => $invoice->type,
                    ];

                    $results['total'][$invoice->currency_code]   += $invoice->amount->total;
                    $results['paid'][$invoice->currency_code]    += $invoice->amount->paid;
                    $results['balance'][$invoice->currency_code] += $invoice->amount->balance;

                    if ($includeLineItemDetail)
                    {
                        if (!empty($invoice->items[0]))
                        {
                            foreach ($invoice->items as $item)
                            {
                                $currency                                                              = Currency::whereCode($invoice->currency_code)->first();
                                $results['records'][$invoice->currency_code][$invoiceIndex]['items'][] =
                                    [
                                        'invoice_id'  => $invoice->id,
                                        'product'     => $item->name,
                                        'description' => $item->description,
                                        'quantity'    => NumberFormatter::format($item->quantity),
                                        'price'       => CurrencyFormatter::format($item->price,$currency),
                                        'subtotal'    => CurrencyFormatter::format($item->amount->subtotal,$currency),
                                        'discount'    => CurrencyFormatter::format($invoice->amount->discount,$currency),
                                        'tax'         => CurrencyFormatter::format($item->amount->tax,$currency),
                                        'total'       => CurrencyFormatter::format($item->amount->total,$currency),

                                    ];

                                if (isset($results['records'][$invoice->currency_code][$invoiceIndex]['items_totals']))
                                {
                                    $results['records'][$invoice->currency_code][$invoiceIndex]['items_totals']['quantity'] += $item->quantity;
                                    $results['records'][$invoice->currency_code][$invoiceIndex]['items_totals']['subtotal'] += round($item->amount->subtotal, 2);
                                    $results['records'][$invoice->currency_code][$invoiceIndex]['items_totals']['discount'] += round($invoice->amount->discount, 2);
                                    $results['records'][$invoice->currency_code][$invoiceIndex]['items_totals']['tax']      += round($item->amount->tax, 2);
                                    $results['records'][$invoice->currency_code][$invoiceIndex]['items_totals']['total']    += round($item->amount->total, 2);
                                }
                                else
                                {
                                    $results['records'][$invoice->currency_code][$invoiceIndex]['items_totals']['quantity'] = $item->quantity;
                                    $results['records'][$invoice->currency_code][$invoiceIndex]['items_totals']['subtotal'] = round($item->amount->subtotal, 2);
                                    $results['records'][$invoice->currency_code][$invoiceIndex]['items_totals']['discount'] = round($invoice->amount->discount, 2);
                                    $results['records'][$invoice->currency_code][$invoiceIndex]['items_totals']['tax']      = round($item->amount->tax, 2);
                                    $results['records'][$invoice->currency_code][$invoiceIndex]['items_totals']['total']    = round($item->amount->total, 2);
                                }

                            }
                        }
                    }
                    if (isset($results['records'][$invoice->currency_code][$invoiceIndex]['items_totals']))
                    {
                        $currency                                                                               = Currency::whereCode($invoice->currency_code)->first();
                        $results['records'][$invoice->currency_code][$invoiceIndex]['items_totals']['quantity'] = NumberFormatter::format($results['records'][$invoice->currency_code][$invoiceIndex]['items_totals']['quantity']);
                        $results['records'][$invoice->currency_code][$invoiceIndex]['items_totals']['subtotal'] = CurrencyFormatter::format(round($results['records'][$invoice->currency_code][$invoiceIndex]['items_totals']['subtotal'], 2), $currency);
                        $results['records'][$invoice->currency_code][$invoiceIndex]['items_totals']['discount'] = CurrencyFormatter::format(round($results['records'][$invoice->currency_code][$invoiceIndex]['items_totals']['discount'], 2), $currency);
                        $results['records'][$invoice->currency_code][$invoiceIndex]['items_totals']['tax']      = CurrencyFormatter::format(round($results['records'][$invoice->currency_code][$invoiceIndex]['items_totals']['tax'], 2), $currency);
                        $results['records'][$invoice->currency_code][$invoiceIndex]['items_totals']['total']    = CurrencyFormatter::format(round($results['records'][$invoice->currency_code][$invoiceIndex]['items_totals']['total'], 2), $currency);
                    }
                }
            }

            $results['client_name'] = $client->name;
            $results['from_date']   = DateFormatter::format($fromDate);
            $results['to_date']     = DateFormatter::format($toDate);

            foreach ($currencyCodes as $code)
            {

                $currency = Currency::whereCode($code)->first();

                $results['total'][$code]   = CurrencyFormatter::format($results['total'][$code], $currency);
                $results['paid'][$code]    = CurrencyFormatter::format($results['paid'][$code], $currency);
                $results['balance'][$code] = CurrencyFormatter::format($results['balance'][$code], $currency);
            }

            $clientData[$client->name]           = $results;
            $clientData['includeLineItemDetail'] = $includeLineItemDetail;

        }
        return $clientData;
    }
}