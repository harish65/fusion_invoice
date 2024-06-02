<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Quotes\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\DocumentNumberSchemes\Models\DocumentNumberScheme;
use FI\Modules\Quotes\Models\Quote;
use FI\Modules\Quotes\Events\QuoteToInvoiceTransition;
use FI\Modules\Quotes\Requests\QuoteToInvoiceRequest;
use FI\Modules\Quotes\Support\QuoteToInvoice;
use FI\Support\DateFormatter;

class QuoteToInvoiceController extends Controller
{
    private $quoteToInvoice;

    public function __construct(QuoteToInvoice $quoteToInvoice)
    {
        $this->quoteToInvoice = $quoteToInvoice;
    }

    public function create()
    {
        return view('quotes._modal_quote_to_invoice')
            ->with('quote_id', request('quote_id'))
            ->with('client_id', request('client_id'))
            ->with('document_number_schemes', DocumentNumberScheme::getList())
            ->with('user_id', auth()->user()->id)
            ->with('invoice_date', DateFormatter::format());
    }

    public function store(QuoteToInvoiceRequest $request)
    {
        $quote = Quote::find($request->input('quote_id'));

        $invoice = $this->quoteToInvoice->convert(
            $quote,
            DateFormatter::unformat($request->input('invoice_date')),
            DateFormatter::incrementDateByDays(DateFormatter::unformat($request->input('invoice_date')), config('fi.invoicesDueAfter')),
            $request->input('document_number_scheme_id')
        );

        event(new QuoteToInvoiceTransition($quote, $invoice));

        return response()->json(['redirectTo' => route('invoices.edit', ['id' => $invoice->id])], 200);
    }
}