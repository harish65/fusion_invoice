<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\ItemLookups\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Currencies\Models\Currency;
use FI\Modules\CustomFields\Support\CustomFieldsParser;
use FI\Modules\CustomFields\Support\CustomFieldsTransformer;
use FI\Modules\ItemLookups\Models\ItemCategory;
use FI\Modules\ItemLookups\Models\ItemLookup;
use FI\Modules\ItemLookups\Requests\ItemLookupRequest;
use FI\Modules\ItemLookups\Requests\ItemLookupUpdateRequest;
use FI\Modules\TaxRates\Models\TaxRate;

class ItemLookupController extends Controller
{
    public function index()
    {
        $itemLookups = ItemLookup::defaultQuery()
                                 ->keywords(request('search'))
                                 ->categoryId(request('category'))
                                 ->sortable(['name' => 'asc'])
                                 ->paginate(config('fi.resultsPerPage'));

        return view('item_lookups.index')
            ->with('itemLookups', $itemLookups)
            ->with('searchPlaceholder', trans('fi.search_items'))
            ->with('categories', ['' => trans('fi.all_categories')] + ItemCategory::getList());
    }

    public function create()
    {
        if (ItemLookup::all()->count() < config('fi.maxItemLookups'))
        {
            return view('item_lookups.form')
                ->with('editMode', false)
                ->with('itemCategory', ItemCategory::getDropDownList())
                ->with('itemLookup', new ItemLookup())
                ->with('customFields', CustomFieldsParser::getFields('item_lookups'))
                ->with('itemPriceFormulas', config('pricing_formula') ? \Addons\PricingFormula\Models\ItemPriceFormula::getList() : [])
                ->with('taxRates', TaxRate::getList());
        }
        else
        {
            return redirect()->route('itemLookups.index')
                             ->with('error', trans('fi.item-lookup-overload', ['max_import' => config('fi.maxItemLookupsImport')]));
        }

    }

    public function store(ItemLookupRequest $request)
    {
        if (ItemLookup::all()->count() < config('fi.maxItemLookups'))
        {
            $itemLookup = ItemLookup::create($request->all());
            // Save the custom fields.
            $customFieldData = CustomFieldsTransformer::transform(request('custom', []), 'item_lookups', $itemLookup);
            $itemLookup->custom->update($customFieldData);

            return redirect()->route('itemLookups.index')
                             ->with('alertSuccess', trans('fi.record_successfully_created'));
        }
        else
        {
            return redirect()->route('itemLookups.index')
                             ->with('error', trans('fi.item-lookup-overload', ['max_import' => config('fi.maxItemLookupsImport')]));
        }

    }

    public function edit($id)
    {
        $itemLookup = ItemLookup::find($id);

        if ($itemLookup->category_id)
        {
            $itemLookup->category_name = ItemCategory::find($itemLookup->category_id)->name;
        }

        return view('item_lookups.form')
            ->with('editMode', true)
            ->with('itemLookup', $itemLookup)
            ->with('customFields', CustomFieldsParser::getFields('item_lookups'))
            ->with('itemCategory', ItemCategory::getDropDownList())
            ->with('itemPriceFormulas', config('pricing_formula') ? \Addons\PricingFormula\Models\ItemPriceFormula::getList() : [])
            ->with('taxRates', TaxRate::getList());
    }

    public function getDetail()
    {

        $custom = request('custom', []);

        if (config('pricing_formula'))
        {
            $itemLookup = \Addons\PricingFormula\Support\PriceCalculator::calculate(request('id'), request('quantity'), $custom);
        }
        else
        {
            $itemLookup                  = ItemLookup::find(request('id'));
            $itemLookup->formula_applied = false;
        }

        $currency     = null;
        $currencyCode = request('currency_code');
        if ($currencyCode)
        {
            $currency             = Currency::getByCode($currencyCode);
            $itemLookup->currency = $currency;
        }
        $itemLookup->original_price = $itemLookup->price;
        $itemLookup->price          = $itemLookup->formatted_numeric_price;
        $itemLookupCustom           = $itemLookup->custom;
        $itemLookup->description    = ($itemLookup->description == null) ? request('description') : $itemLookup->description;

        $customArray  = $labelMappings = $customFieldsType = $quoteCustomFieldsType = $recurringInvoiceCustomFieldsType = [];
        $customFields = CustomFieldsParser::getFields('item_lookups');
        $days         = isset($custom['days']) && $custom['days'] != '' ? $custom['days'] : 0;

        if (request('moduleName') == 'quote')
        {
            $quoteItemsCustomFields = CustomFieldsParser::getFields('quote_items');
            foreach ($quoteItemsCustomFields as $quoteItemsCustomField)
            {
                $quoteCustomFieldsType[$quoteItemsCustomField->column_name] = $quoteItemsCustomField->field_type;
            }

            $itemLookup->quoteCustomFieldsType = $quoteCustomFieldsType;
        }
        if (request('moduleName') == 'invoice')
        {
            $invoiceItemsCustomFields = CustomFieldsParser::getFields('invoice_items');
            foreach ($invoiceItemsCustomFields as $invoiceItemsCustomField)
            {
                $customFieldsType[$invoiceItemsCustomField->column_name] = $invoiceItemsCustomField->field_type;
            }

            $itemLookup->customFieldsType = $customFieldsType;
        }
        if (request('moduleName') == 'recurring_invoice')
        {
            $recurringInvoiceItemsCustomFields = CustomFieldsParser::getFields('recurring_invoice_items');
            foreach ($recurringInvoiceItemsCustomFields as $recurringInvoiceItemsCustomField)
            {
                $recurringInvoiceCustomFieldsType[$recurringInvoiceItemsCustomField->column_name] = $recurringInvoiceItemsCustomField->field_type;
            }

            $itemLookup->recurringInvoiceCustomFieldsType = $recurringInvoiceCustomFieldsType;
        }

        foreach ($customFields as $customField)
        {
            $labelMappings[$customField->column_name] = $customField->field_label;
            if ($customField->field_type == 'image')
            {
                $itemLookupCustom->{$customField->column_name} = '<div class="custom_img">' . $itemLookupCustom->image($customField->column_name, 100) . '</div>';
            }
        }

        foreach ($labelMappings as $fieldName => $fieldLabel)
        {

            $customArray[strtolower($fieldLabel)] = strtolower($fieldLabel) == 'days' && $days > 0 ? $days : $itemLookupCustom->{$fieldName};

        }

        $itemLookup->customFields = $customArray;

        return $itemLookup;
    }

    public function update(ItemLookupUpdateRequest $request, $id)
    {
        $itemLookup = ItemLookup::find($id);

        $itemLookup->fill($request->except(['custom']));

        $itemLookup->save();

        // Save the custom fields.
        $customFieldData = CustomFieldsTransformer::transform(request('custom', []), 'item_lookups', $itemLookup);
        $itemLookup->custom->update($customFieldData);

        return redirect()->route('itemLookups.index')
                         ->with('alertSuccess', trans('fi.record_successfully_updated'));
    }

    public function delete($id)
    {
        try
        {
            ItemLookup::destroy($id);

            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_deleted')], 200);
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function deleteModal()
    {
        try
        {
            return view('layouts._delete_modal')->with('url', request('action'))->with('modalName', request('modalName'))->with('isReload', request('isReload'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }

}
