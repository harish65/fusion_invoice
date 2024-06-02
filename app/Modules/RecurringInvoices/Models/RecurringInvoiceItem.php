<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\RecurringInvoices\Models;

use FI\Modules\CustomFields\Support\CustomFieldsParser;
use FI\Support\CurrencyFormatter;
use FI\Support\NumberFormatter;
use Illuminate\Database\Eloquent\Model;

class RecurringInvoiceItem extends Model
{

    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function amount()
    {
        return $this->hasOne('FI\Modules\RecurringInvoices\Models\RecurringInvoiceItemAmount', 'item_id');
    }

    public function recurringInvoice()
    {
        return $this->belongsTo('FI\Modules\RecurringInvoices\Models\RecurringInvoice');
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
        return $this->hasOne('FI\Modules\CustomFields\Models\RecurringInvoiceItemCustom');
    }

    public function commissions()
    {
        return $this->hasMany('Addons\Commission\Models\RecurringInvoiceItemCommission');
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
        return NumberFormatter::format($this->attributes['price'], $this->recurringInvoice->currency);
    }

    public function getFormattedPriceAttribute()
    {
        return CurrencyFormatter::format($this->attributes['price'], $this->recurringInvoice->currency);
    }

    public function getFormattedDescriptionAttribute()
    {
        return nl2br($this->attributes['description']);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

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
    | Other
    |--------------------------------------------------------------------------
    */

    public function customField($label, $rawHtml = true)
    {
        $customField = config('fi.customFields')->where('tbl_name', 'recurring_invoice_items')->where('field_label', $label)->first();

        if ($customField)
        {
            return CustomFieldsParser::getFieldValue($this->custom, $customField, $rawHtml);
        }

        return null;

    }
}