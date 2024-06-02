<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\Commission\Models;


use FI\Support\DateFormatter;
use Illuminate\Database\Eloquent\Model;


class CommissionType extends Model
{
    protected $table = 'commission_types';

    protected $guarded = ['id'];

    public function getFormattedCreatedAtAttribute()
    {
        return DateFormatter::format($this->attributes['created_at']);
    }

    public static function getDropDownList()
    {

        return ['' => trans('Commission::lang.select_type')] + self::select('name', 'id')->orderBy('name')->pluck('name', 'id')->all();
    }
}
