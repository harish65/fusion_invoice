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

use FI\Modules\Payments\Models\Payment;
use FI\Support\CurrencyFormatter;

class RevenueByClientReport
{
    public function getResults($fromDate, $toDate, $companyProfileId = null, $sort_option = null)
    {
        $results = [
            'clients'     => [],
            'grand_total' => 0,
        ];

        $payments = Payment::select('payments.*')
            ->with(['paymentInvoice.invoice.client'])
            ->join('payment_invoices', 'payments.id', '=', 'payment_invoices.payment_id')
            ->join('invoices', 'invoices.id', '=', 'payment_invoices.invoice_id')
            ->join('clients', 'clients.id', '=', 'invoices.client_id')
            ->where('paid_at', '>=', $fromDate)
            ->where('paid_at', '<=', $toDate)
            ->orderBy('clients.name', 'asc');

        if ($companyProfileId)
        {
            $payments->where('company_profile_id', $companyProfileId);
        }

        $payments = $payments->get();

        foreach ($payments as $payment)
        {
            $year        = date('Y', strtotime($payment->paid_at));
            $monthNumber = date('n', strtotime($payment->paid_at));

            foreach ($payment->paymentInvoice as $paymentInvoice)
            {
                if (isset($results['clients'][$paymentInvoice->invoice->client->name . '##' . $year]['months'][$monthNumber]))
                {
                    $results['clients'][$paymentInvoice->invoice->client->name . '##' . $year]['months'][$monthNumber] += $paymentInvoice->invoice_amount_paid / $paymentInvoice->invoice->exchange_rate;
                }
                else
                {
                    $results['clients'][$paymentInvoice->invoice->client->name . '##' . $year]['months'][$monthNumber] = $paymentInvoice->invoice_amount_paid / $paymentInvoice->invoice->exchange_rate;
                }
            }
        }

        foreach ($results['clients'] as $client => $yearlyData)
        {
            $results['clients'][$client]['year']   = explode('##', $client)[1];
            $results['clients'][$client]['client'] = explode('##', $client)[0];

            foreach ($yearlyData as $result)
            {
                $results['clients'][$client]['total'] = 0;

                foreach (range(1, 12) as $month)
                {
                    if (!isset($results['clients'][$client]['months'][$month]))
                    {
                        $results['clients'][$client]['months'][$month] = 0;
                    }
                    else
                    {
                        $results['clients'][$client]['total']          += $results['clients'][$client]['months'][$month];
                        $results['clients'][$client]['months'][$month] = $results['clients'][$client]['months'][$month];
                    }
                }

                $results['grand_total'] += $results['clients'][$client]['total'];

                $results['clients'][$client]['total'] = $results['clients'][$client]['total'];
            }

        }

        if ($sort_option == 'total_payments')
        {
            $keys = array_column($results['clients'], 'total');
            array_multisort($keys, SORT_DESC, $results['clients']);
        }

        $results['grand_total'] = CurrencyFormatter::format($results['grand_total']);

        return $results;
    }
}