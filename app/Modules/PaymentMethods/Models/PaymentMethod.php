<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\PaymentMethods\Models;

use FI\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use Sortable;

    protected $guarded = ['id'];

    protected $sortable = ['name'];

    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */

    public static function getList()
    {
        return self::orderBy('name')->pluck('name', 'id')->all();
    }

    public static function getByName($name)
    {
        return self::where('name', $name)->first();
    }
}