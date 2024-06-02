<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Http\Middleware;

use Closure;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Invoices\Models\InvoiceItem;
use Illuminate\Http\Request;

class CheckInvoiceStatus
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $requestURL = explode('/', $request->url());
        if (in_array('invoice_copy', $requestURL) || in_array('invoice_to_recurring_invoice_copy', $requestURL))
        {
            return $next($request);
        }
        else
        {
            $invoiceId = $invoiceItemId = null;
            if ((in_array('company_profiles', $requestURL) && in_array('modal_lookup', $requestURL)))
            {
                $invoiceId = request('id');
            }

            if ((in_array('invoice_copy', $requestURL) && in_array('create', $requestURL)) || (in_array('invoice_to_recurring_invoice_copy', $requestURL) && in_array('create', $requestURL)))
            {
                $invoiceId = request('invoice_id');
            }

            if (in_array('invoice_item', $requestURL) && in_array('invoice_mail', $requestURL) && in_array('delete', $requestURL))
            {
                $invoiceItemId = request('id');
            }
            if (in_array('invoices', $requestURL))
            {

                if (in_array('delete', $requestURL))
                {
                    $invoiceId = $request->route('id');
                }
                if (in_array('update_client', $requestURL) || in_array('update_company_profile', $requestURL))
                {
                    $invoiceId = request('id');
                }
                if (in_array('edit', $requestURL))
                {
                    $invoiceId = request('invoice_id');
                }
            }
            if (in_array('clients', $requestURL))
            {
                if ((in_array('ajax', $requestURL) && in_array('modal_lookup', $requestURL)) || (in_array('ajax', $requestURL) && in_array('modal_edit', $requestURL)))
                {
                    $invoiceId = request('id');
                }
            }

            if ($invoiceItemId != null)
            {
                $invoiceItem = InvoiceItem::whereId($invoiceItemId)->first();
                $invoiceId   = $invoiceItem->invoice_id;
            }

        }

        return $next($request);

    }
}
