<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Invoices\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Clients\Models\Client;
use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\CustomFields\Models\CustomField;
use FI\Modules\DocumentNumberSchemes\Models\DocumentNumberScheme;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\RecurringInvoices\Models\RecurringInvoice;
use FI\Modules\RecurringInvoices\Models\RecurringInvoiceItem;
use FI\Modules\RecurringInvoices\Models\RecurringInvoiceTag;
use FI\Modules\RecurringInvoices\Requests\RecurringInvoiceStoreRequest;
use FI\Support\DateFormatter;
use FI\Support\Frequency;

class InvoiceToRecurringInvoiceCopyController extends Controller
{
    public function create()
    {
        $invoice = Invoice::find(request('invoice_id'));

        return view('invoices._modal_recurring_invoice_copy')
            ->with('invoice', $invoice)
            ->with('documentNumberSchemes', DocumentNumberScheme::getList())
            ->with('companyProfiles', CompanyProfile::getList())
            ->with('invoice_date', DateFormatter::format())
            ->with('clients', Client::getDropDownList())
            ->with('user_id', auth()->user()->id)
            ->with('frequencies', Frequency::lists());
    }

    /**
     * @param RecurringInvoiceStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RecurringInvoiceStoreRequest $request)
    {
        try
        {
            $client = Client::find($request->input('client_id'));

            if (false === $client)
            {
                return response()->json(['errors' => [[trans('fi.no_auth_to_create_client')]]], 403);
            }

            $fromInvoice = Invoice::find($request->input('invoice_id'));

            $toRecurringInvoice = RecurringInvoice::create([
                'client_id'                 => $client->id,
                'company_profile_id'        => $request->input('company_profile_id'),
                'document_number_scheme_id' => $request->input('document_number_scheme_id'),
                'currency_code'             => $fromInvoice->currency_code,
                'exchange_rate'             => $fromInvoice->exchange_rate,
                'terms'                     => $fromInvoice->terms,
                'footer'                    => $fromInvoice->footer,
                'template'                  => $fromInvoice->template,
                'summary'                   => $fromInvoice->summary,
                'discount'                  => $fromInvoice->discount,
                'recurring_frequency'       => $request->input('recurring_frequency'),
                'recurring_period'          => $request->input('recurring_period'),
                'next_date'                 => DateFormatter::unformat($request->input('next_date')),
                'stop_date'                 => ($request->input('stop_date') ? DateFormatter::unformat($request->input('stop_date')) : '0000-00-00'),
            ]);

            foreach ($fromInvoice->items as $item)
            {
                $toRecurringInvoiceItem = RecurringInvoiceItem::create([
                    'recurring_invoice_id' => $toRecurringInvoice->id,
                    'name'                 => $item->name,
                    'description'          => $item->description,
                    'quantity'             => $item->quantity,
                    'price'                => $item->price,
                    'tax_rate_id'          => $item->tax_rate_id,
                    'tax_rate_2_id'        => $item->tax_rate_2_id,
                    'display_order'        => $item->display_order,
                ]);
                CustomField::copyCustomFieldValues($item, $toRecurringInvoiceItem);
            }

            foreach ($fromInvoice->tags as $tag)
            {
                RecurringInvoiceTag::create([
                    'recurring_invoice_id' => $toRecurringInvoice->id,
                    'tag_id'               => $tag->tag_id,
                ]);
            }

            // Copy the custom fields
            CustomField::copyCustomFieldValues($fromInvoice, $toRecurringInvoice);

            return response()->json(['success' => true, 'url' => route('recurringInvoices.edit', [$toRecurringInvoice->id])], 200);
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('fi.unknown_error')], 400);
        }
    }
}