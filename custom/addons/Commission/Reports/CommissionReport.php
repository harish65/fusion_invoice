<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\Commission\Reports;

use Addons\Commission\Models\InvoiceItemCommission;
use FI\Support\CurrencyFormatter;
use FI\Support\DateFormatter;

class CommissionReport
{
    public function getResults($fromDate, $toDate, $user = null, $status = null, $type = null)
    {
        $results = [
            'from_date'   => DateFormatter::format($fromDate),
            'to_date'     => DateFormatter::format($toDate),
            'total'       => [],
            'grand_total' => [],
            'records'     => [],
        ];

        $commissionTypes = [
            'invoice_commission',
        ];

        foreach ($commissionTypes as $commissionType)
        {

            $commissions = InvoiceItemCommission::select('invoice_item_commissions.*')
                                                ->with(['type', 'user', 'invoiceItem'])
                                                ->join('invoice_items', 'invoice_item_commissions.invoice_item_id', '=', 'invoice_items.id')
                                                ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                                                ->byDateRange($fromDate, $toDate);

            if ($status)
            {
                $commissions = $commissions->where('invoice_item_commissions.status',$status);
            }

            if ($type)
            {
                $commissions = $commissions->where('invoice_item_commissions.type_id',$type);
            }

            if ($user)
            {
                $commissions = $commissions->where('invoice_item_commissions.user_id',$user);
            }
            else
            {
                $commissions->orderBy('invoice_item_commissions.user_id');
            }

            $commissions = $commissions->get();

            foreach ($commissions as $commission)
            {
                $results['records'][$commissionType][$commission->user->id][] = [
                    'user'          => $commission->user->name,
                    'type'          => $commission->type->name,
                    'status'        => $commission->status,
                    'stop_date'     => $commission->stop_date,
                    'amount'        => $commission->amount,
                    'format_amount' => CurrencyFormatter::format($commission->amount),
                    'client'        => $commission->invoiceItem->invoice->client->name,
                    'product'       => $commission->invoiceItem->name,
                    'number'        => $commission->invoiceItem->invoice->number,
                    'date'          => $commission->invoiceItem->invoice->formatted_invoice_date,
                    'note'          => $commission->note,
                ];
                $results['total'][$commissionType][$commission->user->id]     = isset($results['total'][$commissionType][$commission->user->id]) ? $results['total'][$commissionType][$commission->user->id] + $commission->amount : $commission->amount;
                $results['grand_total'][$commissionType]                      = isset($results['grand_total'][$commissionType]) ? $results['grand_total'][$commissionType] + $commission->amount : $commission->amount;
            }

        }

        foreach ($results['total'] as $commission_types => $values)
        {
            foreach ($values as $user_id => $total)
            {
                $results['total'][$commission_types][$user_id] = CurrencyFormatter::format($total);
            }
        }

        foreach ($results['grand_total'] as $commission_types => $total)
        {
            $results['grand_total'][$commission_types] = CurrencyFormatter::format($total);
        }

        return $results;
    }
}
