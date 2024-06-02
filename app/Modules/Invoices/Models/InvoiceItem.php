<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Invoices\Models;

use FI\Modules\Currencies\Models\Currency;
use FI\Modules\Currencies\Support\CurrencyConverterFactory;
use FI\Modules\CustomFields\Support\CustomFieldsParser;
use FI\Support\CurrencyFormatter;
use FI\Support\NumberFormatter;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{

    protected $guarded = ['id', 'item_id'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function amount()
    {
        return $this->hasOne('FI\Modules\Invoices\Models\InvoiceItemAmount', 'item_id');
    }

    public function invoice()
    {
        return $this->belongsTo('FI\Modules\Invoices\Models\Invoice');
    }

    public function taxRate()
    {
        return $this->belongsTo('FI\Modules\TaxRates\Models\TaxRate');
    }

    public function taxRate2()
    {
        return $this->belongsTo('FI\Modules\TaxRates\Models\TaxRate', 'tax_rate_2_id');
    }

    public function custom()
    {
        return $this->hasOne('FI\Modules\CustomFields\Models\InvoiceItemCustom');
    }

    public function commissions()
    {
        return $this->hasMany('Addons\Commission\Models\InvoiceItemCommission');
    }

    public function paidCommissions()
    {
        return $this->hasMany('Addons\Commission\Models\InvoiceItemCommission')
            ->where('status', '=', 'paid');
    }

    public function itemLookup()
    {
        return $this->hasOne('FI\Modules\ItemLookups\Models\ItemLookup', 'name', 'name');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedQuantityAttribute()
    {
        return NumberFormatter::format($this->attributes['quantity']);
    }

    public function getFormattedNumericPriceAttribute()
    {
        return NumberFormatter::format($this->attributes['price'], $this->invoice->currency);
    }

    public function getFormattedPriceAttribute()
    {
        return CurrencyFormatter::format($this->attributes['price'], $this->invoice->currency);
    }

    public function getFormattedDescriptionAttribute()
    {
        return nl2br($this->attributes['description']);
    }

    public function getFormattedNumericDiscountAttribute()
    {
        return NumberFormatter::format($this->attributes['discount'], $this->invoice->currency);
    }

    public function getFormattedDiscountAttribute()
    {
        if ($this->attributes['discount_type'] == 'flat_amount')
        {
            return CurrencyFormatter::format($this->attributes['discount']);
        }
        elseif ($this->attributes['discount_type'] == 'percentage')
        {
            return $this->attributes['discount'] . '%';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereIn('invoice_items.invoice_id', function ($query) use ($from, $to)
        {
            $query->select('id')
                ->from('invoices')
                ->where('invoice_date', '>=', $from)
                ->where('invoice_date', '<=', $to);
        });
    }

    public function scopeCustomField($query, $includeCustomFields = 0)
    {
        if ($includeCustomFields == 1)
        {
            $query->with('custom');
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */
    public static function getDiscountTypes()
    {
        return ['' => '&#xf05e;', 'percentage' => '&#xf295;', 'flat_amount' => '&#xf0d6;'];
    }

    /*
    |--------------------------------------------------------------------------
    | Other
    |--------------------------------------------------------------------------
    */

    public function customField($label, $rawHtml = true)
    {
        $customField = config('fi.customFields')->where('tbl_name', 'invoice_items')->where('field_label', $label)->first();

        if ($customField)
        {
            return CustomFieldsParser::getFieldValue($this->custom, $customField, $rawHtml);
        }

        return null;

    }

    public function getAlternateCurrency($altCurrency, $price, $field)
    {
        if ($altCurrency != null)
        {
            $currencyConverter = CurrencyConverterFactory::create();
            $exchangeRate      = cache()->remember('exchangeRate', 21600, function () use ($currencyConverter, $altCurrency)
            {
                return $currencyConverter->convert($this->invoice->currency->code, $altCurrency);
            });
            if ($exchangeRate == 1.0000000)
            {
                return false;
            }
            else
            {
                $altPrice = ($price / $exchangeRate);

                $currency = cache()->remember('currency', 21600, function () use ($altCurrency)
                {
                    return Currency::whereCode($altCurrency)->first();
                });

                if ($field == 'discount_type' && $this->attributes[$field] == 'percentage')
                {
                    return null;
                }

                return '( ' . CurrencyFormatter::format($altPrice, $currency) . ' )';
            }
        }
        return null;
    }

}