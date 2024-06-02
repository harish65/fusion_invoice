<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Expenses\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseVendor extends Model
{
    protected $table = 'expense_vendors';

    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsTo('FI\Modules\Expenses\Models\ExpenseCategory');
    }

    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */

    public static function getList()
    {
        return self::whereIn('id', function ($query)
        {
            $query->select('vendor_id')->distinct()->from('expenses');
        })->orderBy('name')->pluck('name', 'id')->all();
    }

    public static function getDropDownList()
    {
        return ['' => trans('fi.select-expense-vendor')] + self::select('name')->orderBy('name')->pluck('name', 'name')->all();
    }


    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedNotesAttribute()
    {
        if ($this->notes && strlen($this->notes) > 40)
        {
            return '<span data-toggle="tooltip" title="' . $this->notes . '">' . mb_substr($this->notes, 0, 40).'...' . '</span>';
        }
        elseif ($this->notes && strlen($this->notes) < 40)
        {
            return $this->notes;
        }
    }
}