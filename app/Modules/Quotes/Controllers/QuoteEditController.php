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
use FI\Modules\Currencies\Models\Currency;
use FI\Modules\CustomFields\Models\QuoteCustom;
use FI\Modules\CustomFields\Models\QuoteItemCustom;
use FI\Modules\CustomFields\Support\CustomFieldsParser;
use FI\Modules\CustomFields\Support\CustomFieldsTransformer;
use FI\Modules\Quotes\Events\AddTransition;
use FI\Modules\Quotes\Events\QuoteToInvoiceTransition;
use FI\Modules\Quotes\Support\QuoteToInvoice;
use FI\Modules\ItemLookups\Models\ItemLookup;
use FI\Modules\Mru\Events\MruLog;
use FI\Modules\Quotes\Models\Quote;
use FI\Modules\Quotes\Models\QuoteItem;
use FI\Modules\Quotes\Requests\QuoteUpdateRequest;
use FI\Modules\Quotes\Support\QuoteTemplates;
use FI\Modules\TaxRates\Models\TaxRate;
use FI\Support\DateFormatter;
use FI\Support\Statuses\QuoteStatuses;
use FI\Traits\ReturnUrl;
use Illuminate\Support\Facades\Storage;

class QuoteEditController extends Controller
{
    use ReturnUrl;

    public function edit($id)
    {
        $quote = Quote::with(['items.amount.item.quote.currency'])->find($id);
        event(new MruLog(['module' => 'quotes', 'action' => 'edit', 'id' => $id, 'title' => $quote->number . ' ' . $quote->client->name]));

        return view('quotes.edit')
            ->with('quote', $quote)
            ->with('statuses', QuoteStatuses::lists())
            ->with('currencies', Currency::getList())
            ->with('taxRates', TaxRate::getList())
            ->with('customFields', CustomFieldsParser::getFields('quotes'))
            ->with('quoteItemCustomFields', CustomFieldsParser::getFields('quote_items'))
            ->with('returnUrl', $this->getReturnUrl())
            ->with('templates', QuoteTemplates::lists())
            ->with('itemCount', count($quote->quoteItems))
            ->with('discountTypes', Quote::getDiscountTypes())
            ->with('allowLineItemDiscounts', config('fi.allowLineItemDiscounts') == 1 ? true : false);
    }

    public function update(QuoteUpdateRequest $request, $id)
    {
           
        $input               = $request->except(['items', 'custom', 'apply_exchange_rate']);
        $input['quote_date'] = DateFormatter::unformat($input['quote_date']);
        $input['expires_at'] = DateFormatter::unformat($input['expires_at']);

        // Save the quote.
        $quote = Quote::find($id);
        $quote->fill($input);
        $updatedFields = $quote->getDirty();

        if (isset($updatedFields['status']))
        {
            event(new AddTransition($quote, 'status_changed', $quote->getOriginal('status'), $quote->status));

            if ($quote->status == 'approved' && config('fi.convertQuoteWhenApproved'))
            {
                $quoteToInvoice = new QuoteToInvoice();

                $invoice = $quoteToInvoice->convert(
                    $quote,
                    date('Y-m-d'),
                    DateFormatter::incrementDateByDays(date('Y-m-d'), config('fi.invoicesDueAfter')),
                    config('fi.invoiceGroup')
                );

                event(new QuoteToInvoiceTransition($quote, $invoice));
            }
        }
        $quote->save();

        $response = '';

        // Save the custom fields.
        $customFieldData = CustomFieldsTransformer::transform(request('custom', []), 'quotes', $quote);
        $quote->custom->update($customFieldData);

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

            $item['apply_exchange_rate'] = $request->input('apply_exchange_rate');

            if ($item['name'] == '' and $item['price'] == '')
            {
                continue;
            }

            if (!isset($item['id']) or (!$item['id']))
            {
                $saveItemAsLookup = $item['save_item_as_lookup'];
                unset($item['save_item_as_lookup']);

                $quoteItem = QuoteItem::create($item);

                if ($item['data_custom_item_delete'] != 'no')
                {
                    if ($item['item_lookup_id'] != '' && isset($item['item_lookup_id']))
                    {
                        $itemCustomImage = findItemLookupsCustomImageField($item['item_lookup_id'], false, 'quote_items');
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
                    $customFieldData = CustomFieldsTransformer::transform($item['custom'], 'quote_items', $quoteItem);
                    $quoteItem->custom->update($customFieldData);
                }

                if ($saveItemAsLookup)
                {
                    if (ItemLookup::all()->count() < config('fi.maxItemLookups'))
                    {
                        $itemLookup = ItemLookup::updateOrCreate(['name' => $item['name']], [
                            'name'          => $item['name'],
                            'description'   => $item['description'],
                            'price'         => $item['price'],
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
                $quoteItem = QuoteItem::find($item['id']);
                $quoteItem->fill($item);
                $quoteItem->save();


                if ($item['item_lookup_id'] != '' && isset($item['item_lookup_id']))
                {
                    $itemCustomImage = findItemLookupsCustomImageField($item['item_lookup_id'], $item['id'], 'quote_items');
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
                        removeCustomFieldImage($itemCustomImage['customFieldsColumnName'], 'quote_items', $item['id']);
                    }
                }

                if (isset($item['custom']))
                {
                    $customFieldData = CustomFieldsTransformer::transform($item['custom'], 'quote_items', $quoteItem);
                    $quoteItem->custom->update($customFieldData);
                }

                $saveItemAsLookup = $item['save_item_as_lookup'];

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
            event(new AddTransition($quote, 'updated'));
        }
        return response()->json($response, 200);
    }

    public function refreshEdit($id)
    {
        $quote = Quote::with(['items.amount.item.quote.currency'])->find($id);

        return view('quotes._edit')
            ->with('quote', $quote)
            ->with('statuses', QuoteStatuses::lists())
            ->with('currencies', Currency::getList())
            ->with('taxRates', TaxRate::getList())
            ->with('customFields', CustomFieldsParser::getFields('quotes'))
            ->with('quoteItemCustomFields', CustomFieldsParser::getFields('quote_items'))
            ->with('returnUrl', $this->getReturnUrl())
            ->with('templates', QuoteTemplates::lists())
            ->with('itemCount', count($quote->quoteItems))
            ->with('discountTypes', Quote::getDiscountTypes())
            ->with('allowLineItemDiscounts', config('fi.allowLineItemDiscounts') == 1 ? true : false);
    }

    public function refreshTotals()
    {
        return view('quotes._edit_totals')
            ->with('quote', Quote::with(['items.amount.item.quote.currency'])->find(request('id')));
    }

    public function refreshTo()
    {
        return view('quotes._edit_to')
            ->with('quote', Quote::find(request('id')));
    }

    public function refreshFrom()
    {
        return view('quotes._edit_from')
            ->with('quote', Quote::find(request('id')));
    }

    public function updateClient()
    {
        Quote::where('id', request('id'))->update(['client_id' => request('client_id')]);
    }

    public function updateCompanyProfile()
    {
        Quote::where('id', request('id'))->update(['company_profile_id' => request('company_profile_id')]);
    }

    public function deleteImage($id, $columnName)
    {

        if (request('item_custom_id') != 'null' && request('item_custom_id') != '')
        {
            $customFields = QuoteItemCustom::whereQuoteItemId(request('item_custom_id'))->first();
            $existingFile = 'quote_items' . DIRECTORY_SEPARATOR . $customFields->{$columnName};
        }
        else
        {
            $customFields = QuoteCustom::whereQuoteId($id)->first();
            $existingFile = 'quotes' . DIRECTORY_SEPARATOR . $customFields->{$columnName};
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

        $quote = Quote::find(request('id'));

        return view('quotes._ajax_add_line_item')
            ->with('quote_id', request('id'))
            ->with('key', request('key'))
            ->with('discountTypes', Quote::getDiscountTypes())
            ->with('currencies', Currency::getList())
            ->with('taxRates', TaxRate::getList())
            ->with('quoteItemCustomFields', CustomFieldsParser::getFields('quote_items'))
            ->with('allowLineItemDiscounts', config('fi.allowLineItemDiscounts') == 1 ? true : false)
            ->with('currencyCode', $quote->currency_code)
            ->render();
    }

}
