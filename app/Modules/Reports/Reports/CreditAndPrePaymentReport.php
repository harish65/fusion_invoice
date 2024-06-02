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

use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Payments\Models\Payment;
use FI\Support\CurrencyFormatter;
use FI\Support\DateFormatter;
use FI\Modules\Currencies\Models\Currency;

class CreditAndPrePaymentReport
{
    public function getCreditResults($clientId, $fromDate, $toDate, $status = null)
    {
        $credits = Invoice::select('invoices.*')->with('client', 'companyProfile', 'amount')
                          ->join('invoice_amounts', 'invoices.id', '=', 'invoice_amounts.invoice_id')
                          ->type('credit_memo')
                          ->where('invoice_date', '>=', $fromDate)
                          ->where('invoice_date', '<=', $toDate)
                          ->orderBy('invoice_date');

        if ($clientId)
        {
            $credits->whereIn('client_id', explode(',', $clientId));
        }

        if ($status == 'yes')
        {
            $credits->where('invoice_amounts.balance', '<', 0);
        }
        elseif ($status == 'no')
        {
            $credits->where('invoice_amounts.balance', '=', '0.0000');
        }

        $credits = $credits->get();

        $results = [];

        foreach ($credits as $credit)
        {
            $results[$credit->client->name] = [
                'from_date' => DateFormatter::format($fromDate),
                'to_date'   => DateFormatter::format($toDate),
                'total'     => [],
                'paid'      => [],
                'balance'   => [],
                'records'   => [],
            ];
        }

        foreach ($credits as $credit)
        {
            $results[$credit->client->name]['records'][$credit->currency_code][] = [
                'company'                => $credit->companyProfile->company,
                'number'                 => $credit->number,
                'total'                  => $credit->amount->total,
                'paid'                   => $credit->amount->paid,
                'balance'                => $credit->amount->balance,
                'formatted_total'        => $credit->amount->formatted_total,
                'formatted_paid'         => $credit->amount->formatted_paid,
                'formatted_balance'      => $credit->amount->formatted_balance,
                'formatted_invoice_date' => $credit->formatted_invoice_date
            ];
            $results[$credit->client->name]['total'][$credit->currency_code]     = isset($results[$credit->client->name]['total'][$credit->currency_code]) ? $results[$credit->client->name]['total'][$credit->currency_code] + $credit->amount->total : $credit->amount->total;
            $results[$credit->client->name]['paid'][$credit->currency_code]      = isset($results[$credit->client->name]['paid'][$credit->currency_code]) ? $results[$credit->client->name]['paid'][$credit->currency_code] + $credit->amount->paid : $credit->amount->paid;
            $results[$credit->client->name]['balance'][$credit->currency_code]   = isset($results[$credit->client->name]['balance'][$credit->currency_code]) ? $results[$credit->client->name]['balance'][$credit->currency_code] + $credit->amount->balance : $credit->amount->balance;
        }

        foreach ($results as $key => $result)
        {
            foreach ($result['total'] as $code => $value)
            {
                $currency = Currency::whereCode($code)->first();

                $results[$key]['total'][$code]   = CurrencyFormatter::format($result['total'][$code], $currency);
                $results[$key]['paid'][$code]    = CurrencyFormatter::format($result['paid'][$code], $currency);
                $results[$key]['balance'][$code] = CurrencyFormatter::format($result['balance'][$code], $currency);
            }
        }

        return $results;
    }

    public function getPrePaymentResults($clientId, $fromDate, $toDate, $status = null)
    {
        $pre_payments = Payment::with('client')->prePayment()
                               ->where('paid_at', '>=', $fromDate)
                               ->where('paid_at', '<=', $toDate)
                               ->orderBy('paid_at');
        if ($clientId)
        {
            $pre_payments->whereIn('client_id', explode(',', $clientId));
        }

        if ($status == 'yes')
        {
            $pre_payments->where('remaining_balance', '>', 0);
        }
        elseif ($status == 'no')
        {
            $pre_payments->where('remaining_balance', '=', '0.0000');
        }

        $pre_payments = $pre_payments->get();

        $results = [];

        foreach ($pre_payments as $pre_payment)
        {
            $results[$pre_payment->client->name] = [
                'from_date' => DateFormatter::format($fromDate),
                'to_date'   => DateFormatter::format($toDate),
                'total'     => [],
                'paid'      => [],
                'balance'   => [],
                'records'   => [],
            ];
        }

        foreach ($pre_payments as $pre_payment)
        {
            $results[$pre_payment->client->name]['records'][$pre_payment->currency_code][] = [
                'total'                  => $pre_payment->amount,
                'paid'                   => $pre_payment->amount - $pre_payment->remaining_balance,
                'balance'                => $pre_payment->remaining_balance,
                'formatted_total'        => $pre_payment->formatted_amount,
                'formatted_paid'         => $pre_payment->formatted_paid_amount,
                'formatted_balance'      => $pre_payment->formatted_remaining_balance,
                'formatted_invoice_date' => $pre_payment->formatted_paid_at
            ];

            $results[$pre_payment->client->name]['total'][$pre_payment->currency_code]     = isset($results[$pre_payment->client->name]['total'][$pre_payment->currency_code]) ? $results[$pre_payment->client->name]['total'][$pre_payment->currency_code] + $pre_payment->amount : $pre_payment->amount;
            $results[$pre_payment->client->name]['paid'][$pre_payment->currency_code]      = isset($results[$pre_payment->client->name]['paid'][$pre_payment->currency_code]) ? $results[$pre_payment->client->name]['paid'][$pre_payment->currency_code] + ($pre_payment->amount - $pre_payment->remaining_balance) : ($pre_payment->amount - $pre_payment->remaining_balance);
            $results[$pre_payment->client->name]['balance'][$pre_payment->currency_code]   = isset($results[$pre_payment->client->name]['balance'][$pre_payment->currency_code]) ? $results[$pre_payment->client->name]['balance'][$pre_payment->currency_code] + $pre_payment->remaining_balance : $pre_payment->remaining_balance;
        }

        foreach ($results as $key => $result)
        {
            foreach ($result['total'] as $code => $value)
            {
                $currency = Currency::whereCode($code)->first();

                $results[$key]['total'][$code]   = CurrencyFormatter::format($result['total'][$code], $currency);
                $results[$key]['paid'][$code]    = CurrencyFormatter::format($result['paid'][$code], $currency);
                $results[$key]['balance'][$code] = CurrencyFormatter::format($result['balance'][$code], $currency);
            }
        }

        return $results;
    }
}