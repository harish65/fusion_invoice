<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\ItemLookups\Models;

use FI\Modules\CustomFields\Support\CustomFieldsParser;
use FI\Support\CurrencyFormatter;
use FI\Support\NumberFormatter;
use FI\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;

class ItemLookup extends Model
{
    use Sortable;

    /**
     * Guarded properties
     * @var array
     */
    protected $guarded = ['id'];

    protected $sortable = ['name', 'description', 'price', 'item_categories.name'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function taxRate()
    {
        return $this->belongsTo('FI\Modules\TaxRates\Models\TaxRate');
    }

    public function taxRate2()
    {
        return $this->belongsTo('FI\Modules\TaxRates\Models\TaxRate', 'tax_rate_2_id');
    }

    public function category()
    {
        return $this->belongsTo('FI\Modules\ItemLookups\Models\ItemCategory');
    }

    public function custom()
    {
        return $this->hasOne('FI\Modules\CustomFields\Models\ItemLookupCustom');
    }

    public function formula()
    {
        return $this->belongsTo('Addons\PricingFormula\Models\ItemPriceFormula');
    }

    /*
    |--------------------------------------------------------------------------
    | Mutators
    |--------------------------------------------------------------------------
    */

    public function setCurrencyAttribute($value)
    {
        $this->attributes['currency'] = $value;
    }


    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedPriceAttribute()
    {
        return CurrencyFormatter::format($this->attributes['price']);
    }

    public function getFormattedNumericPriceAttribute()
    {
        if (isset($this->attributes['currency']) && !empty($this->attributes['currency']))
        {
            return NumberFormatter::format($this->attributes['price'], $this->attributes['currency']);
        }
        else
        {
            return NumberFormatter::format($this->attributes['price']);
        }
    }

    public function getFormattedTaxRateAttribute()
    {
        if ($this->attributes['tax_rate_id'] == -1)
        {
            return trans('fi.system_default');
        }
        else
        {
            return isset($this->taxRate->name) ? $this->taxRate->name : '';
        }

    }

    public function getFormattedTaxRate2Attribute()
    {

        if ($this->attributes['tax_rate_2_id'] == -1)
        {
            return trans('fi.system_default');
        }
        else
        {
            return isset($this->taxRate2->name) ? $this->taxRate2->name : '';
        }
    }

    public function getFormattedDescriptionAttribute()
    {
        if ($this->description && strlen($this->description) > 40)
        {
            return '<span data-toggle="tooltip" title="' . $this->description . '">' . mb_substr($this->description, 0, 40).'...' . '</span>';
        }
        elseif ($this->description && strlen($this->description) < 40)
        {
            return $this->description;
        }
    }
    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeKeywords($query, $keywords)
    {
        if ($keywords)
        {
            $keywords = strtolower($keywords);

            $query->where('item_lookups.name', 'like', '%' . $keywords . '%')
                ->orWhere('item_lookups.description', 'like', '%' . $keywords . '%')
                ->orWhere('item_lookups.price', 'like', '%' . $keywords . '%')
                ->orWhere('item_categories.name', 'like', '%' . $keywords . '%');
        }

        return $query;
    }

    public function scopeCategoryId($query, $categoryId = null)
    {
        if ($categoryId)
        {
            $query->where('category_id', $categoryId);
        }

        return $query;
    }

    public function scopeDefaultQuery($query)
    {
        return $query->select('item_lookups.*', 'item_categories.name AS category_name')
            ->leftJoin('item_categories', 'item_categories.id', '=', 'item_lookups.category_id');
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
    | Other
    |--------------------------------------------------------------------------
    */

    public function customField($label, $rawHtml = true)
    {
        $customField = config('fi.customFields')->where('tbl_name', 'item_lookups')->where('field_label', $label)->first();

        if ($customField)
        {
            return CustomFieldsParser::getFieldValue($this->custom, $customField, $rawHtml);
        }

        return null;

    }
}