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
use FI\Modules\Quotes\Events\AddTransition;
use FI\Modules\Quotes\Events\QuoteApproved;
use FI\Modules\Quotes\Events\QuoteRejected;
use FI\Modules\Quotes\Events\QuoteViewed;
use FI\Modules\Quotes\Models\Quote;
use FI\Modules\Users\Models\User;
use FI\Support\FileNames;
use FI\Support\PDF\PDFFactory;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class ClientCenterPublicQuoteController extends Controller
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

        $quote = Quote::where('url_key', $urlKey)->first();
        if ($quote == null)
        {
            return view('errors.link_expired');
        }
        app()->setLocale($quote->client->language);

        $userId = User::whereUserType('system')->first()->id;

        event(new QuoteViewed($quote));
        event(new AddTransition($quote, 'email_opened', '', '', $userId));

        return view('client_center.quotes.public')
            ->with('quote', $quote)
            ->with('token', $token)
            ->with('urlKey', $urlKey)
            ->with('attachments', $quote->clientAttachments);
    }

    public function pdf($urlKey)
    {
        $quote = Quote::with('items.taxRate', 'items.taxRate2', 'items.amount.item.quote', 'items.quote')
            ->where('url_key', $urlKey)->first();

        event(new QuoteViewed($quote));

        $pdf = PDFFactory::create();

        $html = darkModeForInvoiceAndQuoteTemplate($quote->html);

        $pdf->download($html, FileNames::quote($quote));
    }

    public function html($urlKey)
    {
        $quote = Quote::with('items.taxRate', 'items.taxRate2', 'items.amount.item.quote', 'items.quote')
            ->where('url_key', $urlKey)->first();

        return $quote->html;
    }

    public function approve($urlKey, $token = null)
    {
        $quote = Quote::where('url_key', $urlKey)->first();
        if ($quote->status != 'approved')
        {
            $quote->status = 'approved';

            $quote->save();

            event(new QuoteApproved($quote));
        }
        return redirect()->route('clientCenter.public.quote.show', [$urlKey, $token]);
    }

    public function reject($urlKey, $token = null)
    {
        $quote = Quote::where('url_key', $urlKey)->first();
        if ($quote->status != 'rejected')
        {
            $quote->status = 'rejected';

            $quote->save();

            event(new QuoteRejected($quote));
        }
        return redirect()->route('clientCenter.public.quote.show', [$urlKey, $token]);
    }

    public function approveAndRejectModal()
    {
        try
        {
            return view('client_center.quotes._modal_approve_and_reject')
                ->with('url', request('action'))
                ->with('message', request('message'))
                ->with('modalName', request('modalName'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }
}
