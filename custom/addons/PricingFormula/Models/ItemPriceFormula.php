<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\PricingFormula\Models;

use Illuminate\Database\Eloquent\Model;
use FI\Traits\Sortable;

class ItemPriceFormula extends Model
{
    use Sortable;

    protected $table = 'item_price_formulas';

    protected $guarded = ['id'];

    protected $sortable = ['name'];

    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */

    public static function getList()
    {
        return ['' => trans('PricingFormula::lang.select_formula')] + self::orderBy('name')->pluck('name', 'id')->all();
    }

}