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

use FI\Modules\CustomFields\Support\CustomFieldsParser;
use FI\Support\CurrencyFormatter;
use FI\Support\DateFormatter;
use FI\Support\NumberFormatter;
use FI\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use Sortable;

    protected $table = 'expenses';

    protected $fillable = ['user_id', 'client_id', 'company_profile_id', 'expense_date', 'category_name', 'amount', 'tax', 'vendor_name', 'description', 'category_id', 'vendor_id'];
    protected $sortable = ['id', 'expense_date', 'expense_categories.name', 'description', 'amount'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function attachments()
    {
        return $this->morphMany('FI\Modules\Attachments\Models\Attachment', 'attachable');
    }

    public function category()
    {
        return $this->belongsTo('FI\Modules\Expenses\Models\ExpenseCategory');
    }

    public function client()
    {
        return $this->belongsTo('FI\Modules\Clients\Models\Client');
    }

    public function companyProfile()
    {
        return $this->belongsTo('FI\Modules\CompanyProfiles\Models\CompanyProfile');
    }

    public function custom()
    {
        return $this->hasOne('FI\Modules\CustomFields\Models\ExpenseCustom');
    }

    public function invoice()
    {
        return $this->belongsTo('FI\Modules\Invoices\Models\Invoice');
    }

    public function vendor()
    {
        return $this->belongsTo('FI\Modules\Expenses\Models\ExpenseVendor');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getAttachmentPermissionOptionsAttribute()
    {
        return [
            '0' => trans('fi.not_visible'),
            '1' => trans('fi.visible'),
        ];
    }

    public function getFormattedAmountAttribute()
    {
        return CurrencyFormatter::format($this->amount);
    }

    public function getFormattedTaxAttribute()
    {
        return CurrencyFormatter::format($this->tax);
    }

    public function getTotalAttribute()
    {
        return ($this->amount + $this->tax);
    }

    public function getFormattedTotalAttribute()
    {
        return CurrencyFormatter::format($this->amount + $this->tax);
    }

    public function getFormattedExpenseDateAttribute()
    {
        return DateFormatter::format($this->expense_date);
    }

    public function getFormattedNumericAmountAttribute()
    {
        return NumberFormatter::format($this->amount);
    }

    public function getFormattedNumericTaxAttribute()
    {
        return NumberFormatter::format($this->tax);
    }

    public function getHasBeenBilledAttribute()
    {
        if ($this->invoice_id)
        {
            return true;
        }

        return false;
    }

    public function getIsBillableAttribute()
    {
        if ($this->client_id)
        {
            return true;
        }

        return false;
    }

    public function getFormattedDescriptionAttribute()
    {
        if ($this->description && strlen($this->description) > 40)
        {
            return '<span data-toggle="tooltip" title="' . $this->description . '">' . mb_substr($this->description, 0, 40) . '...' . '</span>';
        }
        elseif ($this->description && strlen($this->description) < 40)
        {
            return $this->description;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */

    public static function getHeaders()
    {
        return ['id', 'vendor', 'date', 'category', 'description', 'amount'];
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeCategoryId($query, $categoryId = null)
    {
        if ($categoryId)
        {
            $query->where('expenses.category_id', $categoryId);
        }

        return $query;
    }

    public function scopeCompanyProfileId($query, $companyProfileId = null)
    {
        if ($companyProfileId)
        {
            $query->where('company_profile_id', $companyProfileId);
        }

        return $query;
    }

    public function scopeDefaultQuery($query)
    {
        return $query->select('expenses.*', 'expense_categories.name AS category_name',
            'expense_vendors.name AS vendor_name', 'clients.name AS client_name')
            ->leftJoin('expense_categories', 'expense_categories.id', '=', 'expenses.category_id')
            ->leftJoin('expense_vendors', 'expense_vendors.id', '=', 'expenses.vendor_id')
            ->leftJoin('clients', 'clients.id', '=', 'expenses.client_id');
    }

    public function scopeKeywords($query, $keywords = null)
    {
        if ($keywords)
        {
            $keywords = strtolower($keywords);

            $query->where('expenses.expense_date', 'like', '%' . $keywords . '%')
                ->orWhere('expenses.description', 'like', '%' . $keywords . '%')
                ->orWhere('expense_vendors.name', 'like', '%' . $keywords . '%')
                ->orWhere('clients.name', 'like', '%' . $keywords . '%')
                ->orWhere('expense_categories.name', 'like', '%' . $keywords . '%');
        }

        return $query;
    }

    public function scopeStatus($query, $status = null)
    {
        if ($status)
        {
            switch ($status)
            {
                case 'billed':
                    $query->where('invoice_id', '<>', 0);
                    break;
                case 'not_billed':
                    $query->where('client_id', '<>', 0)->where('invoice_id', '=', 0);
                    break;
                case 'not_billable':
                    $query->where('client_id', 0);
                    break;
            }
        }

        return $query;
    }

    public function scopeVendorId($query, $vendorId = null)
    {
        if ($vendorId)
        {
            $query->where('vendor_id', $vendorId);
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
        $customField = config('fi.customFields')->where('tbl_name', 'expenses')->where('field_label', $label)->first();

        if ($customField)
        {
            return CustomFieldsParser::getFieldValue($this->custom, $customField, $rawHtml);
        }

        return null;

    }
}