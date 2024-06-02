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

use Carbon\Carbon;
use FI\Http\Controllers\Controller;
use FI\Modules\Currencies\Models\Currency;
use FI\Modules\CustomFields\Models\InvoiceCustom;
use FI\Modules\CustomFields\Models\InvoiceItemCustom;
use FI\Modules\CustomFields\Support\CustomFieldsParser;
use FI\Modules\CustomFields\Support\CustomFieldsTransformer;
use FI\Modules\Invoices\Events\AddTransition;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Invoices\Models\InvoiceItem;
use FI\Modules\Invoices\Requests\InvoiceUpdateRequest;
use FI\Modules\Invoices\Support\InvoiceTemplates;
use FI\Modules\ItemLookups\Models\ItemLookup;
use FI\Modules\Mru\Events\MruLog;
use FI\Modules\Payments\Models\Payment;
use FI\Modules\Tags\Models\Tag;
use FI\Modules\TaxRates\Models\TaxRate;
use FI\Support\DateFormatter;
use FI\Support\Statuses\InvoiceStatuses;
use FI\Traits\ReturnUrl;
use Illuminate\Support\Facades\Storage;

class InvoiceEditController extends Controller
{
    use ReturnUrl;

    public function edit($id)
    {
        $invoice         = Invoice::with(['items.amount.item.invoice.currency'])->find($id);
        $creditMemoCount = Invoice::creditMemoListForClient($invoice->client_id);
        $prePayment      = Payment::prePaymentListForClient($invoice->client_id);
        $invoiceList     = Invoice::invoiceListForClient($invoice->client_id);

        event(new MruLog(['module' => 'invoices', 'action' => 'edit', 'id' => $id, 'title' => $invoice->number . ' ' . $invoice->client->name]));
        $selectedTags = [];

        foreach ($invoice->tags as $tagDetail)
        {
            $selectedTags[] = $tagDetail->tag->name;
        }
        $overlay = $invoice->type == 'invoice' ? request()->get('overlay', config('fi.allowEditInvoiceStatus') == 'draft' ? false : true) : true;

        return view('invoices.edit')
            ->with('invoice', $invoice)
            ->with('statuses', ($invoice->type != 'credit_memo') ? InvoiceStatuses::lists() : InvoiceStatuses::creditMemoLists())
            ->with('currencies', Currency::getList())
            ->with('taxRates', TaxRate::getList())
            ->with('customFields', CustomFieldsParser::getFields('invoices'))
            ->with('invoiceItemCustomFields', CustomFieldsParser::getFields('invoice_items'))
            ->with('returnUrl', $this->getReturnUrl())
            ->with('templates', InvoiceTemplates::lists())
            ->with('creditMemoCount', count($creditMemoCount))
            ->with('prePaymentCount', count($prePayment))
            ->with('invoiceCount', count($invoiceList))
            ->with('itemCount', count($invoice->invoiceItems))
            ->with('tags', Tag::whereTagEntity('sales')->pluck('name', 'name'))
            ->with('selectedTags', $selectedTags)
            ->with('discountTypes', InvoiceItem::getDiscountTypes())
            ->with('allowLineItemDiscounts', $invoice->type != 'credit_memo' && config('fi.allowLineItemDiscounts') == 1 ? true : false)
            ->with('overlay', $overlay)
            ->with('noChange', request()->get('no_change', ''))
            ->with('invoiceOverlayStatus', ($overlay && ($invoice->status == 'sent' || $invoice->paidStatus)))
            ->with('creditMemoOverlayStatus', ($invoice->type == 'credit_memo' && $invoice->status == 'applied'));

    }

    public function update(InvoiceUpdateRequest $request, $id)
    {
        // Unformat the invoice dates.
        $invoiceInput                 = $request->except(['items', 'custom', 'apply_exchange_rate', 'tags']);
        $invoiceInput['invoice_date'] = DateFormatter::unformat($invoiceInput['invoice_date']);
        $invoiceInput['due_at']       = DateFormatter::unformat($invoiceInput['due_at']);

        // Save the invoice.
        $invoice = Invoice::find($id);
        $invoice->fill($invoiceInput);
        $updatedFields = $invoice->getDirty();
        if (isset($updatedFields['status']))
        {
            event(new AddTransition($invoice, 'status_changed', $invoice->getOriginal('status'), $invoice->status));
        }
        $invoice->save();

        $manageTags = manageTags($invoice, 'invoice_tag_updated', 'invoice_tag_deleted', 'Invoices');

        $tags    = isset($manageTags) ? $manageTags : '';
        $tag_ids = [];

        if (is_array($tags))
        {
            foreach ($tags as $tag)
            {
                $tag = Tag::firstOrNew(['name' => $tag, 'tag_entity' => 'sales'])->fill(['name' => $tag, 'tag_entity' => 'sales']);

                $tag->save();

                $tag_ids[] = $tag->id;
            }
            foreach ($tag_ids as $tag_id)
            {
                $invoice->tags()->insert(['invoice_id' => $invoice->id, 'tag_id' => $tag_id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            }
        }

        // Save the custom fields.
        $customFieldData = CustomFieldsTransformer::transform(request('custom', []), 'invoices', $invoice);
        $invoice->custom->update($customFieldData);

        $response = '';

        // Save the items.
        foreach ($request->input('items') as $item)
        {
            if (isset($item['discount_type']) && $item['discount_type'] != '')
            {
                if ($item['price'] > 0)
                {
                    if ($item['discount'] != '' && $item['discount_type'] != '')
                    {
                        $priceAndPreviousPrice  = calculateDiscount($item['discount_type'], $item['discount'], $item['price']);
                        $item['previous_price'] = $priceAndPreviousPrice['previous_price'];
                    }
                }
            }

            $item['apply_exchange_rate'] = request('apply_exchange_rate');

            if ($item['name'] == '' and $item['price'] == '')
            {
                continue;
            }

            if (!isset($item['id']) or (!$item['id']))
            {

                $saveItemAsLookup = $item['save_item_as_lookup'];
                unset($item['save_item_as_lookup']);
                $item['price'] = ($invoice->type == 'credit_memo') ? (-1 * abs($item['price'])) : abs($item['price']);

                $invoiceItem = InvoiceItem::create($item);

                if ($item['data_custom_item_delete'] != 'no')
                {
                    if ($item['item_lookup_id'] != '' && isset($item['item_lookup_id']))
                    {
                        $itemCustomImage = findItemLookupsCustomImageField($item['item_lookup_id'], false, 'invoice_items');
                        if ($itemCustomImage['customFieldsColumnName'] != '' && $itemCustomImage['itemLookupImageColumn'] != '')
                        {
                            if ($item['custom'][$itemCustomImage['customFieldsColumnName']] == '')
                            {
                                $item['custom'][$itemCustomImage['customFieldsColumnName']] = $itemCustomImage['itemLookUpCustom'][$itemCustomImage['itemLookupImageColumn']];
                            }
                        }
                    }
                }

                if (isset($item['custom']))
                {
                    $customFieldData = CustomFieldsTransformer::transform($item['custom'], 'invoice_items', $invoiceItem);
                    $invoiceItem->custom->update($customFieldData);
                }
                if ($saveItemAsLookup)
                {
                    if (ItemLookup::all()->count() < config('fi.maxItemLookups'))
                    {
                        $itemLookup = ItemLookup::updateOrCreate(['name' => $item['name']], [
                            'name'          => $item['name'],
                            'description'   => $item['description'],
                            'price'         => abs($item['price']),
                            'tax_rate_id'   => isset($item['tax_rate_id']) ? $item['tax_rate_id'] : -1,
                            'tax_rate_2_id' => isset($item['tax_rate_2_id']) ? $item['tax_rate_2_id'] : -1,
                        ]);
                        if (isset($item['custom']))
                        {
                            $customFieldData = CustomFieldsTransformer::sync($item['custom'], 'item_lookups', $itemLookup);
                            $itemLookup->custom->update($customFieldData);
                        }
                    }
                    else
                    {
                        $response = ['error' => trans('fi.item-lookup-overload', ['max_import' => config('fi.maxItemLookupsImport')])];
                    }
                }
            }
            else
            {
                $invoiceItem = InvoiceItem::find($item['id']);
                $invoiceItem->fill($item);
                $invoiceItem->save();

                if ($item['item_lookup_id'] != '' && isset($item['item_lookup_id']))
                {
                    $itemCustomImage = findItemLookupsCustomImageField($item['item_lookup_id'], $item['id'], 'invoice_items');
                    if ($item['data_custom_item_delete'] != 'no')
                    {
                        if ($itemCustomImage['customFieldsColumnName'] != '' && $itemCustomImage['itemLookupImageColumn'] != '')
                        {
                            if ($item['custom'][$itemCustomImage['customFieldsColumnName']] == '')
                            {
                                $item['custom'][$itemCustomImage['customFieldsColumnName']] = $itemCustomImage['itemLookUpCustom'][$itemCustomImage['itemLookupImageColumn']];
                            }
                        }
                    }
                    else
                    {
                        removeCustomFieldImage($itemCustomImage['customFieldsColumnName'], 'invoice_items', $item['id']);
                    }
                }

                if (isset($item['custom']))
                {
                    $customFieldData = CustomFieldsTransformer::transform($item['custom'], 'invoice_items', $invoiceItem);
                    $invoiceItem->custom->update($customFieldData);
                }

                $saveItemAsLookup = $item['save_item_as_lookup'];
                $item['price']    = ($invoice->type == 'credit_memo') ? (-1 * abs($item['price'])) : abs($item['price']);
                if ($saveItemAsLookup)
                {
                    if (ItemLookup::all()->count() < config('fi.maxItemLookups'))
                    {
                        $itemLookup = ItemLookup::updateOrCreate(['name' => $item['name']], [
                            'name'          => $item['name'],
                            'description'   => $item['description'],
                            'price'         => abs($item['price']),
                            'tax_rate_id'   => isset($item['tax_rate_id']) ? $item['tax_rate_id'] : -1,
                            'tax_rate_2_id' => isset($item['tax_rate_2_id']) ? $item['tax_rate_2_id'] : -1,
                        ]);
                        if (isset($item['custom']))
                        {
                            $customFieldData = CustomFieldsTransformer::sync($item['custom'], 'item_lookups', $itemLookup);
                            $itemLookup->custom->update($customFieldData);
                        }
                    }
                    else
                    {
                        $response = ['error' => trans('fi.item-lookup-overload', ['max_import' => config('fi.maxItemLookupsImport')])];
                    }
                }
            }
        }

        if (!isset($updatedFields['status']))
        {
            if ($invoice->type == 'credit_memo')
            {
                event(new AddTransition($invoice, 'credit_memo_updated'));
            }
            else
            {
                event(new AddTransition($invoice, 'updated'));
            }
        }
        return response()->json($response);
    }

    public function refreshEdit($id)
    {
        $invoice         = Invoice::with(['items.amount.item.invoice.currency'])->find($id);
        $creditMemoCount = Invoice::creditMemoListForClient($invoice->client_id);
        $prePayment      = Invoice::creditMemoListForClient($invoice->client_id);
        $invoiceList     = Invoice::invoiceListForClient($invoice->client_id);
        $selectedTags    = [];

        foreach ($invoice->tags as $tagDetail)
        {
            $selectedTags[] = $tagDetail->tag->name;
        }

        $overlay = $invoice->type == 'invoice' ? request()->get('overlay', config('fi.allowEditInvoiceStatus') == 'draft' ? false : true) : true;

        return view('invoices._edit')
            ->with('invoice', $invoice)
            ->with('statuses', InvoiceStatuses::lists())
            ->with('currencies', Currency::getList())
            ->with('taxRates', TaxRate::getList())
            ->with('customFields', CustomFieldsParser::getFields('invoices'))
            ->with('invoiceItemCustomFields', CustomFieldsParser::getFields('invoice_items'))
            ->with('returnUrl', $this->getReturnUrl())
            ->with('templates', InvoiceTemplates::lists())
            ->with('creditMemoCount', count($creditMemoCount))
            ->with('prePaymentCount', count($prePayment))
            ->with('invoiceCount', count($invoiceList))
            ->with('itemCount', count($invoice->invoiceItems))
            ->with('tags', Tag::whereTagEntity('sales')->pluck('name', 'name'))
            ->with('selectedTags', $selectedTags)
            ->with('discountTypes', InvoiceItem::getDiscountTypes())
            ->with('allowLineItemDiscounts', $invoice->type != 'credit_memo' && config('fi.allowLineItemDiscounts') == 1 ? true : false)
            ->with('overlay', $overlay)
            ->with('noChange', request()->get('no_change', ''))
            ->with('invoiceOverlayStatus', ($overlay && ($invoice->status == 'sent' || $invoice->paidStatus)))
            ->with('creditMemoOverlayStatus', ($invoice->type == 'credit_memo' && $invoice->status == 'applied'));
    }

    public function refreshTotals()
    {
        return view('invoices._edit_totals')
            ->with('invoice', Invoice::with(['items.amount.item.invoice.currency'])->find(request('id')));
    }

    public function refreshTo()
    {
        return view('invoices._edit_to')
            ->with('invoice', Invoice::find(request('id')));
    }

    public function refreshFrom()
    {
        return view('invoices._edit_from')
            ->with('invoice', Invoice::find(request('id')));
    }

    public function updateClient()
    {
        Invoice::where('id', request('id'))->update(['client_id' => request('client_id')]);
    }

    public function updateCompanyProfile()
    {
        Invoice::where('id', request('id'))->update(['company_profile_id' => request('company_profile_id')]);
    }

    public function deleteImage($id, $columnName)
    {
        if (request('item_custom_id') != 'null' && request('item_custom_id') != '')
        {
            $customFields = InvoiceItemCustom::whereInvoiceItemId(request('item_custom_id'))->first();
            $existingFile = 'invoice_items' . DIRECTORY_SEPARATOR . $customFields->{$columnName};
        }
        else
        {
            $customFields = InvoiceCustom::whereInvoiceId($id)->first();
            $existingFile = 'invoices' . DIRECTORY_SEPARATOR . $customFields->{$columnName};
        }
        if (Storage::disk(CustomFieldsTransformer::STORAGE_DISK_NAME)->exists($existingFile))
        {
            try
            {
                Storage::disk(CustomFieldsTransformer::STORAGE_DISK_NAME)->delete($existingFile);
                $customFields->{$columnName} = null;
                $customFields->save();
            }
            catch (Exception $e)
            {
            }
        }
    }

    public function addLineItem()
    {
        $invoice = Invoice::with(['items.amount.item.invoice.currency'])->find(request('id'));

        return view('invoices._ajax_add_line_item')
            ->with('invoice', request('id'))
            ->with('key', request('key'))
            ->with('taxRates', TaxRate::getList())
            ->with('customFields', CustomFieldsParser::getFields('invoices'))
            ->with('invoiceItemCustomFields', CustomFieldsParser::getFields('invoice_items'))
            ->with('allowLineItemDiscounts', $invoice->type != 'credit_memo' && config('fi.allowLineItemDiscounts') == 1 ? true : false)
            ->with('discountTypes', InvoiceItem::getDiscountTypes())
            ->with('currencyCode', $invoice->currency_code)
            ->render();
    }

    public function updateSummaryAndTags($id)
    {
        try
        {
            $invoice          = Invoice::find($id);
            $invoice->summary = request('summary');
            $invoice->save();

            $manageTags = manageTags($invoice, 'invoice_tag_updated', 'invoice_tag_deleted', 'Invoices');

            $tags    = isset($manageTags) ? $manageTags : request('tags', []);
            $tag_ids = [];

            if (is_array($tags))
            {
                foreach ($tags as $tag)
                {
                    $tag = Tag::firstOrNew(['name' => $tag, 'tag_entity' => 'sales'])->fill(['name' => $tag, 'tag_entity' => 'sales']);

                    $tag->save();

                    $tag_ids[] = $tag->id;
                }
                foreach ($tag_ids as $tag_id)
                {
                    $invoice->tags()->insert(['invoice_id' => $invoice->id, 'tag_id' => $tag_id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
                }
            }

            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_updated')], 200);
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('fi.unknown_error')], 401);
        }
    }

}
