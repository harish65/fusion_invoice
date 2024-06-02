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

use FI\Support\CurrencyFormatter;
use FI\Support\DateFormatter;
use FI\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class RecurringInvoiceItemCommission extends Model
{

    use Sortable;

    protected $table = 'recurring_invoice_item_commissions';

    protected $guarded = ['id'];

    protected $sortable = [
        'recurring_invoices.id',
        'clients.name',
        'username',
        'commission_type',
        'recurring_invoice_items.name',
        'recurring_invoice_item_amounts.subtotal',
        'recurring_invoice_item_commissions.amount',
        'recurring_invoice_item_commissions.stop_date',
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
        return $this->belongsTo('FI\Modules\RecurringInvoices\Models\RecurringInvoiceItem', 'recurring_invoice_item_id', 'id');
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

    public function getStopDateEpochAttribute()
    {
        return $this->stop_date != null && $this->stop_date != '0000-00-00' ? DateFormatter::format($this->stop_date, false) : '';
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeCompanyProfileId($query, $companyProfileId)
    {
        if ($companyProfileId)
        {
            $query->where('invoices.company_profile_id', $companyProfileId);
        }

        return $query;
    }

    public function scopeStatus($query, $status = null)
    {
        switch ($status)
        {
            case 'active':
                $query->whereDate('recurring_invoices.stop_date', '0000-00-00')->orWhereDate('recurring_invoices.stop_date','>=', Carbon::now()->toDateString());
                break;
            case 'inactive':
                $query->where('recurring_invoices.stop_date','<=', Carbon::now()->toDateString());
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
                  ->orWhere('recurring_invoice_items.name', 'like', '%' . $keywords . '%')
                  ->orWhere('recurring_invoice_item_commissions.amount', 'like', '%' . $keywords . '%')
                  ->orWhere('recurring_invoice_item_commissions.note', 'like', '%' . $keywords . '%');
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | Other
    |--------------------------------------------------------------------------
    */

    public static function getStatusList()
    {
        return [
            'active'   => trans('Commission::lang.active'),
            'inactive' => trans('Commission::lang.inactive'),
            ''         => trans('Commission::lang.all'),
        ];
    }


}
