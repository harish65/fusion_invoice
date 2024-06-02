<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Exports\Models;

use FI\Modules\CustomFields\Support\CustomFieldsParser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ExportMapping extends Model
{
    protected $table = 'export_mappings';

    protected $guarded = [];

    protected $casts = [
        'description' => 'array',
        'is_default'  => 'boolean',
    ];

    public static function getMappingsByType($type)
    {
        return self::where('type', $type)->get();
    }

    public static function getLabelByType($type)
    {
        switch ($type)
        {
            case 'Clients':
                return trans('fi.export_clients');
                break;
            case 'Quotes':
                return trans('fi.export_quotes');
                break;
            case 'QuoteItems':
                return trans('fi.export_quote_items');
                break;
            case 'Invoices':
                return trans('fi.export_invoices');
                break;
            case 'InvoiceItems':
                return trans('fi.export_invoice_items');
                break;
            case 'Payments':
                return trans('fi.export_payments');
                break;
            case 'Expenses':
                return trans('fi.export_expenses');
                break;
            case 'ItemLookups':
                return trans('fi.export_item_lookups');
                break;
            default:
                return trans('fi.export_data');
        }
    }

    public static function getBasicFieldsByType($type)
    {
        switch ($type)
        {
            case 'Clients':
                return Schema::getColumnListing('clients');
                break;
            case 'Quotes':
                return [
                    'number', 'created_at', 'updated_at', 'expires_at',
                    'terms', 'footer', 'url_key', 'currency_code', 'exchange_rate',
                    'template', 'summary', 'group', 'client_name',
                    'client_email', 'client_address', 'client_city',
                    'client_state', 'client_zip', 'client_country',
                    'user_name', 'user_email',
                    'company', 'company_address',
                    'company_city', 'company_state',
                    'company_zip', 'company_country',
                    'subtotal', 'tax', 'total',
                ];
                break;
            case 'QuoteItems':
                return [
                    'number', 'created_at', 'name',
                    'description', 'quantity', 'price', 'tax_rate_1_name',
                    'tax_rate_1_percent', 'tax_rate_1_is_compound',
                    'tax_rate_1_amount', 'tax_rate_2_name',
                    'tax_rate_2_percent', 'tax_rate_2_is_compound',
                    'tax_rate_2_amount', 'subtotal', 'tax', 'total',
                ];
                break;
            case 'Invoices':
                return [
                    'number', 'created_at', 'updated_at', 'invoice_date',
                    'due_at', 'terms', 'footer', 'url_key', 'currency_code',
                    'exchange_rate', 'template', 'summary', 'group', 'client_name',
                    'client_email', 'client_address', 'client_city',
                    'client_state', 'client_zip', 'client_country',
                    'user_name', 'user_email',
                    'company', 'company_address',
                    'company_city', 'company_state',
                    'company_zip', 'company_country',
                    'subtotal', 'tax', 'total', 'paid', 'balance',
                ];
                break;
            case 'InvoiceItems':
                return [
                    'number', 'created_at', 'name', 'description', 'quantity', 'price', 'tax_rate_1_name', 'tax_rate_1_percent',
                    'tax_rate_1_is_compound', 'tax_rate_1_amount', 'tax_rate_2_name', 'tax_rate_2_percent', 'tax_rate_2_is_compound',
                    'tax_rate_2_amount', 'subtotal', 'tax', 'total',
                ];
                break;
            case 'Payments':
                return [
                    'number', 'paid_at', 'invoice_amount_paid',
                    'payment_method', 'note',
                ];
                break;
            case 'Expenses':
                return [
                    'expense_date', 'description', 'amount',
                    'client_name', 'category_name', 'vendor_name',
                    'invoice_number', 'user_name', 'company',
                ];
                break;
            case 'ItemLookups':
                return [
                    'id', 'created_at', 'name', 'description', 'price', 'item_category', 'tax_rate_1_name', 'tax_rate_1_percent',
                    'tax_rate_1_is_compound', 'tax_rate_2_name', 'tax_rate_2_percent', 'tax_rate_2_is_compound',
                ];
                break;
            default:
                return [];
        }
    }

    public static function getAllFieldsByType($type)
    {
        $fields       = self::getBasicFieldsByType($type);
        $customFields = [];
        $custom       = [];
        if ($type == 'Clients')
        {
            $customFields = CustomFieldsParser::getFields('clients');
        }
        elseif ($type == 'Quotes')
        {
            $customFields = CustomFieldsParser::getFields('quotes');
        }
        elseif ($type == 'QuoteItems')
        {
            $customFields = CustomFieldsParser::getFields('quote_items');
        }
        elseif ($type == 'Invoices')
        {
            $customFields = CustomFieldsParser::getFields('invoices');
        }
        elseif ($type == 'InvoiceItems')
        {
            $customFields = CustomFieldsParser::getFields('invoice_items');
        }
        elseif ($type == 'Payments')
        {
            $customFields = CustomFieldsParser::getFields('payments');
        }
        elseif ($type == 'Expenses')
        {
            $customFields = CustomFieldsParser::getFields('expenses');
        }
        elseif ($type == 'ItemLookups')
        {
            $customFields = CustomFieldsParser::getFields('item_lookups');
        }
        foreach ($customFields as $customField)
        {
            array_push($custom, $customField->field_label);
        }
        if (!empty($custom))
        {
            $fields = array_merge($fields, $custom);
        }
        return $fields;
    }

    public static function getDefaultMappingByType($type)
    {
        return $default = self::where([['type', '=', $type], ['is_default', '=', '1']])->first();
    }
}