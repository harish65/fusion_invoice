<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\ClientCenter\Controllers;

use Carbon\Carbon;
use FI\Http\Controllers\Controller;
use FI\Modules\Invoices\Events\AddTransition;
use FI\Modules\Invoices\Events\InvoiceViewed;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Merchant\Support\MerchantFactory;
use FI\Modules\Users\Models\User;
use FI\Support\FileNames;
use FI\Support\PDF\PDFFactory;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class ClientCenterPublicInvoiceController extends Controller
{
    public function show($urlKey, $token = null)
    {

        $invoiceSecureLink = config('fi.secure_link', 0);

        if ($invoiceSecureLink == 1)
        {

            try
            {
                $date = ($token == null) ? '1970-01-01' : Crypt::decrypt($token);
            }
            catch (DecryptException $e)
            {
                return view('errors.link_expired');
            }

            if ((config('fi.secure_link') == 1) && (Carbon::now()->format('Y-m-d') > $date))
            {
                return view('errors.link_expired');
            }
        }

        $invoice = Invoice::where('url_key', $urlKey)->first();
        if ($invoice == null)
        {
            return view('errors.link_expired');
        }
        app()->setLocale($invoice->client->language);

        $userId = User::whereUserType('system')->first()->id;

        event(new InvoiceViewed($invoice));
        event(new AddTransition($invoice, 'email_opened', '', '', $userId));

        return view('client_center.invoices.public')
            ->with('invoice', $invoice)
            ->with('urlKey', $urlKey)
            ->with('merchantDrivers', MerchantFactory::getDrivers(true))
            ->with('attachments', $invoice->clientAttachments);
    }

    public function pdf($urlKey)
    {
        $invoice = Invoice::with('items.taxRate', 'items.taxRate2', 'items.amount.item.invoice', 'items.invoice')
            ->where('url_key', $urlKey)->first();

        event(new InvoiceViewed($invoice));

        $pdf = PDFFactory::create();

        $html = darkModeForInvoiceAndQuoteTemplate($invoice->html);

        $pdf->download($html, FileNames::invoice($invoice));
    }

    public function html($urlKey)
    {
        $invoice = Invoice::with('items.taxRate', 'items.taxRate2', 'items.amount.item.invoice', 'items.invoice')
            ->where('url_key', $urlKey)->first();

        return $invoice->html;
    }
}
