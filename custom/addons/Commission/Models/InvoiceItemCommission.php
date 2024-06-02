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

use FI\Modules\Users\Models\User;
use FI\Support\CurrencyFormatter;
use FI\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;

class InvoiceItemCommission extends Model
{
    use Sortable;

    protected $table = 'invoice_item_commissions';

    protected $guarded = ['id'];

    protected $sortable = [
        'invoices.number' => ['LENGTH(number)', 'number'],
        'invoices.invoice_date',
        'clients.name',
        'username',
        'commission_type',
        'invoice_items.name',
        'invoice_item_amounts.subtotal',
        'invoice_item_commissions.amount',
        'invoice_item_commissions.status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function type()
    {
        return $this->belongsTo('Addons\Commission\Models\CommissionType');
    }

    public function user()
    {
        return $this->belongsTo('FI\Modules\Users\Models\User');
    }

    public function invoiceItem()
    {
        return $this->belongsTo('FI\Modules\Invoices\Models\InvoiceItem', 'invoice_item_id', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedAmountAttribute()
    {
        return CurrencyFormatter::format($this->attributes['amount']);
    }

    public function getFormattedStatusAttribute()
    {
        switch ($this->attributes['status'])
        {
            case 'approved':
                return '<span class="text-info">' . ucfirst($this->attributes['status']) . '</span>';
                break;
            case 'cancelled':
                return '<span class="text-danger">' . ucfirst($this->attributes['status']) . '</span>';
                break;
            case 'new':
                return '<span class="text-default">' . ucfirst($this->attributes['status']) . '</span>';
                break;
            case 'paid':
                return '<span class="text-success">' . ucfirst($this->attributes['status']) . '</span>';
                break;
        }

    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeStatus($query, $status = null)
    {
        switch ($status)
        {
            case 'approved':
                $query->where('invoice_item_commissions.status', 'approved');
                break;
            case 'cancelled':
                $query->where('invoice_item_commissions.status', 'cancelled');
                break;
            case 'new':
                $query->where('invoice_item_commissions.status', 'new');
                break;
            case 'paid':
                $query->where('invoice_item_commissions.status', 'paid');
                break;
        }

        return $query;
    }


    public function scopeKeywords($query, $keywords = null)
    {
        if ($keywords)
        {
            $keywords = strtolower($keywords);

            $query->where('users.name', 'like', '%' . $keywords . '%')
                  ->orWhere('clients.name', 'like', '%' . $keywords . '%')
                  ->orWhere('commission_types.name', 'like', '%' . $keywords . '%')
                  ->orWhere('invoice_items.name', 'like', '%' . $keywords . '%')
                  ->orWhere('invoice_item_commissions.amount', 'like', '%' . $keywords . '%')
                  ->orWhere('invoice_item_commissions.note', 'like', '%' . $keywords . '%');
        }

        return $query;
    }

    public function scopeCompanyProfileId($query, $companyProfileId)
    {
        if ($companyProfileId)
        {
            $query->where('invoices.company_profile_id', $companyProfileId);
        }

        return $query;
    }

    public function scopeByDateRange($query, $from, $to)
    {
        if($from != '' && $to != '')
        {
            return $query->whereIn('invoice_items.invoice_id', function ($query) use ($from, $to)
            {
                $query->select('id')
                      ->from('invoices')
                      ->where('invoice_date', '>=', $from)
                      ->where('invoice_date', '<=', $to);
            });
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Other
    |--------------------------------------------------------------------------
    */

    public static function getStatusList()
    {
        return [
            ''          => trans('Commission::lang.select_status'),
            'approved'  => trans('Commission::lang.approved'),
            'cancelled' => trans('Commission::lang.cancelled'),
            'new'       => trans('Commission::lang.new'),
            'paid'      => trans('Commission::lang.paid'),
        ];
    }

    public static function getBulkStatusList()
    {
        return [
            'approved'  => '<span class="text-info">' . trans('Commission::lang.approved') . '</span>',
            'cancelled' => '<span class="text-danger">' . trans('Commission::lang.cancelled') . '</span>',
            'new'       => '<span class="text-default">' . trans('Commission::lang.new') . '</span>',
            'paid'      => '<span class="text-success">' . trans('Commission::lang.paid') . '</span>',
        ];
    }


    public static function getUserDropDownList()
    {
        return ['' => trans('Commission::lang.select_user')] + User::select('name', 'id')
                                                                   ->whereIn('user_type', array_keys(User::getUserTypes()))
                                                                   ->orderBy('name')
                                                                   ->pluck('name', 'id')
                                                                   ->all();
    }

}
