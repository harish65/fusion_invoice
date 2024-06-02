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

use FI\Modules\Expenses\Models\Expense;
use FI\Modules\Invoices\Models\Invoice;
use FI\Support\CurrencyFormatter;
use FI\Support\DateFormatter;
use FI\Support\NumberFormatter;

class TaxSummaryReport
{
    public function getResults($fromDate, $toDate, $companyProfileId = null, $excludeUnpaidInvoices = 0, $dateFilterBy)
    {
        $results = [
            'from_date' => DateFormatter::format($fromDate),
            'to_date'   => DateFormatter::format($toDate),
            'total'     => 0,
            'paid'      => 0,
            'remaining' => 0,
            'records'   => [],
        ];

        $invoices = Invoice::select('invoices.*')->with(['items.taxRate', 'items.taxRate2', 'items.amount'])->leftJoin('payment_invoices', function ($join)
        {
            $join->on('payment_invoices.invoice_id', '=', 'invoices.id');
        })->join('payments', function ($join) use ($dateFilterBy, $fromDate, $toDate)
        {
            $join->on('payments.id', '=', 'payment_invoices.payment_id')->where(function ($q) use ($dateFilterBy, $fromDate, $toDate)
            {
                if ($dateFilterBy == 'payment_date')
                {
                    return $q->where('paid_at', '>=', $fromDate)->where('paid_at', '<=', $toDate);
                }
            });
        })->where(function ($q) use ($dateFilterBy, $fromDate, $toDate)
        {
            if ($dateFilterBy == 'invoice_date')
            {
                $q->where('invoice_date', '>=', $fromDate)->where('invoice_date', '<=', $toDate);
            }
        })->where('status', '<>', 'canceled');

        if ($companyProfileId)
        {
            $invoices->where('company_profile_id', $companyProfileId);

            $expenseTax = (Expense::where('expense_date', '>=', $fromDate)
            ->where('expense_date', '<=', $toDate)
            ->where('company_profile_id', $companyProfileId)
            ->sum('tax')) ?: 0;
        } 
        else 
        {
            $expenseTax = (Expense::where('expense_date', '>=', $fromDate)
            ->where('expense_date', '<=', $toDate)
            ->sum('tax')) ?: 0;
        }

        if ($excludeUnpaidInvoices)
        {
            $invoices->paid();
        }

        $invoices = $invoices->get();

        foreach ($invoices as $invoice)
        {
            foreach ($invoice->items as $invoiceItem)
            {

                if ($invoiceItem->tax_rate_id)
                {
                    $key = $invoiceItem->taxRate->name . ' (' . NumberFormatter::format($invoiceItem->taxRate->percent, null, 3) . '%)';

                    if (isset($results['records'][$key]['taxable_amount']))
                    {
                        $results['records'][$key]['taxable_amount'] += $invoiceItem->amount->subtotal / $invoice->exchange_rate;
                        $results['records'][$key]['taxes'] += $invoiceItem->amount->tax_1 / $invoice->exchange_rate;
                    }
                    else
                    {
                        $results['records'][$key]['taxable_amount'] = $invoiceItem->amount->subtotal / $invoice->exchange_rate;
                        $results['records'][$key]['taxes']          = $invoiceItem->amount->tax_1 / $invoice->exchange_rate;
                    }
                }

                if ($invoiceItem->tax_rate_2_id)
                {
                    $key = $invoiceItem->taxRate2->name . ' (' . NumberFormatter::format($invoiceItem->taxRate2->percent, null, 3) . '%)';

                    if (isset($results['records'][$key]['taxable_amount']))
                    {
                        if ($invoiceItem->taxRate2->is_compound)
                        {
                            $results['records'][$key]['taxable_amount'] += ($invoiceItem->amount->subtotal + $invoiceItem->amount->tax_1) / $invoice->exchange_rate;
                        }
                        else
                        {
                            $results['records'][$key]['taxable_amount'] += $invoiceItem->amount->subtotal / $invoice->exchange_rate;
                        }

                        $results['records'][$key]['taxes'] += $invoiceItem->amount->tax_2 / $invoice->exchange_rate;
                    }
                    else
                    {
                        if ($invoiceItem->taxRate2->is_compound)
                        {
                            $results['records'][$key]['taxable_amount'] = ($invoiceItem->amount->subtotal + $invoiceItem->amount->tax_2) / $invoice->exchange_rate;
                        }
                        else
                        {
                            $results['records'][$key]['taxable_amount'] = $invoiceItem->amount->subtotal / $invoice->exchange_rate;
                        }

                        $results['records'][$key]['taxes'] = $invoiceItem->amount->tax_2 / $invoice->exchange_rate;
                    }
                }
            }
        }

        foreach ($results['records'] as $key => $result)
        {
            $results['total']                           = $results['total'] + $result['taxes'];
            $results['records'][$key]['taxable_amount'] = CurrencyFormatter::format($result['taxable_amount']);
            $results['records'][$key]['taxes']          = CurrencyFormatter::format($result['taxes']);
        }

        $results['paid']      = CurrencyFormatter::format($expenseTax);
        $results['remaining'] = CurrencyFormatter::format($results['total'] - $expenseTax);
        $results['total']     = CurrencyFormatter::format($results['total']);

        return $results;
    }
}