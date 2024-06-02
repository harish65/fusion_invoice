<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\PaymentCenter\Repositories;

use FI\Modules\Invoices\Models\Invoice;

class PaymentCenterInvoiceRepository
{
    public function search($search)
    {
        if ($search)
        {
            if ($search['name'] or $search['phone'] or $search['invoice_number'])
            {
                $invoices = Invoice::with(['client', 'amount.invoice.currency'])
                    ->select('invoices.*')
                    ->join('clients', 'clients.id', '=', 'invoices.client_id');

                if ($search['name'])
                {
                    $invoices->where('clients.name', 'like', '%' . $search['name'] . '%');
                }

                if ($search['phone'])
                {
                    $invoices->where('clients.phone', 'like', '%' . $search['phone'] . '%');
                }

                if ($search['invoice_number'])
                {
                    $invoices->where('invoices.number', 'like', '%' . $search['invoice_number'] . '%');
                }

                return $invoices->orderBy('invoices.created_at', 'desc')->paginate();
            }
        }
    }
}