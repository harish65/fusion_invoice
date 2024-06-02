<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Users\Models;

use FI\Modules\CustomFields\Support\CustomFieldsParser;
use FI\Support\DateFormatter;
use FI\Traits\Sortable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use HasApiTokens, Authenticatable, CanResetPassword, Sortable, SoftDeletes;

    protected $table = 'users';

    protected $guarded = ['id', 'password', 'password_confirmation'];

    protected $hidden = ['password', 'remember_token'];

    protected $sortable = ['name', 'email'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function client()
    {
        return $this->belongsTo('FI\Modules\Clients\Models\Client');
    }

    public function custom()
    {
        return $this->hasOne('FI\Modules\CustomFields\Models\UserCustom');
    }

    public function expenses()
    {
        return $this->hasMany('FI\Modules\Expenses\Models\Expense');
    }

    public function invoices()
    {
        return $this->hasMany('FI\Modules\Invoices\Models\Invoice');
    }

    public function quotes()
    {
        return $this->hasMany('FI\Modules\Quotes\Models\Quote');
    }

    public function permissions()
    {
        return $this->hasMany(UserPermissions::class);
    }

    public function tasks()
    {
        return $this->hasMany('FI\Modules\TaskList\Models\Task');
    }

    public function tasksByAssignee()
    {
        return $this->hasMany('FI\Modules\TaskList\Models\Task', 'assignee_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedLastLoginAtAttribute()
    {
        return $this->attributes['last_login_at'] ? DateFormatter::format($this->attributes['last_login_at'], true) : null;
    }

    public function getFormattedStatusAttribute()
    {
        return $this->attributes['status'] == 1 ? '<span title="Active"> <i class="fa fa-check-circle btn btn-sm btn-success" style="margin-left: 9px;"></i></span>' : '<span title="Inactive"> <i class="fa fa-times-circle btn btn-sm btn-danger" style="margin-left: 9px;"></i></span>';
    }

    public function getFormattedNameAttribute()
    {
        return $this->attributes['deleted_at'] != '' ? '<strike>' . $this->attributes['name'] . '</strike>' : $this->attributes['name'];
    }

    public function getFromEmailAttribute()
    {
        return (config('fi.showInvoicesFrom') == 'userWhoCreatedInvoice')
            ? $this->attributes['email'] : config('fi.mailFromAddress');
    }

    /*
    |--------------------------------------------------------------------------
    | Mutators
    |--------------------------------------------------------------------------
    */

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeUserType($query, $userType)
    {
        if (!empty($userType))
        {
            $query->where('user_type', $userType);
        }

        return $query;
    }

    /*
      |--------------------------------------------------------------------------
      | Static Methods
      |--------------------------------------------------------------------------
     */

    public static function getDropDownList($id)
    {
        return self::select(DB::raw("CASE WHEN email != '' THEN CONCAT(COALESCE(`name`,''),'<span>[',COALESCE(`email`,''),']</span>') ELSE `name` END AS user_name"), 'id')->whereNotIn('id', [$id])->whereStatus(1)->whereIn('user_type', ['admin', 'standard_user'])->orderBy('name')->pluck('user_name', 'id')->all();
    }

    public static function getUserList()
    {
        return self::whereNotIn('user_type', ['client', 'system'])->pluck('name', 'id')->all();
    }

    public static function getAllUsersList()
    {
        return ['' => trans('fi.select_user')] + self::select(DB::raw("CASE WHEN email != '' THEN CONCAT(COALESCE(`name`,''),'<span>[',COALESCE(`email`,''),']</span>') ELSE `name` END AS user_name"), 'id')->whereStatus(1)->whereIn('user_type', ['admin', 'standard_user'])->orderBy('name')->pluck('user_name', 'id')->all();
    }

    /*
    |--------------------------------------------------------------------------
    | Other
    |--------------------------------------------------------------------------
    */

    public function customField($label, $rawHtml = true)
    {
        $customField = config('fi.customFields')->where('tbl_name', 'users')->where('field_label', $label)->first();

        if ($customField)
        {
            return CustomFieldsParser::getFieldValue($this->custom, $customField, $rawHtml);
        }

        return null;

    }

    public static function getAllUserTypes()
    {
        return [
            'admin'         => trans('fi.admin'),
            'standard_user' => trans('fi.standard_user'),
            'client'        => trans('fi.client'),
        ];
    }

    public static function getUserTypes()
    {
        return [
            'admin'         => trans('fi.admin'),
            'standard_user' => trans('fi.standard_user'),
        ];
    }

    public static function getStatus()
    {
        return [
            '1' => trans('fi.active'),
            '0' => trans('fi.inactive'),
        ];
    }

    public function hasPermission($module, $permission)
    {
        $userPermission = $this->permissions()->whereModule($module)->first();
        if (!empty($userPermission))
        {
            return 1 == $userPermission->$permission;
        }

        return false;
    }

    public function getAvatar($size = 40, $isRounded = true)
    {
        return view('users.avatar')
            ->with('user', $this)
            ->with('size', $size)
            ->with('isRounded', $isRounded)->render();
    }

    public static function invoiceColumnSetting()
    {
        return
            [
                "status"       => ["on", "notSortable", "", ""],
                "invoice"      => ["on", "sortable", "", "number"],
                "recurring_id" => ["on", "notSortable", "", ""],
                "date"         => ["on", "sortable", "", "invoice_date"],
                "due"          => ["on", "sortable", "", "due_at"],
                "client"       => ["on", "sortable", "", "clients.name"],
                "summary"      => ["on", "sortable", "", "summary"],
                "tags"         => ["on", "notSortable", " text-center", ""],
                "total"        => ["on", "sortable", "text-right pr-4", "invoice_amounts.total"],
                "balance"      => ["on", "sortable", "text-right pr-4", "invoice_amounts.balance"],
            ];
    }

    public static function recurringInvoiceColumnSettings()
    {
        return
            [
                "id"        => ["on", "sortable", "", "id"],
                "client"    => ["on", "sortable", "", "clients.name"],
                "summary"   => ["on", "sortable", "", "summary"],
                "next_date" => ["on", "sortable", "", "next_date"],
                "stop_date" => ["on", "sortable", "", "stop_date"],
                "every"     => ["on", "notSortable", "", ""],
                "tags"      => ["on", "notSortable", " text-center", ""],
                "total"     => ["on", "sortable", "text-right pr-4", "recurring_invoice_amounts.total"],
            ];
    }

    public static function quoteColumnSettings()
    {
        return
            [
                "status"   => ["on", "notSortable", "", ""],
                "quote"    => ["on", "sortable", "", "number"],
                "date"     => ["on", "sortable", "", "quote_date"],
                "expires"  => ["on", "sortable", "", "expires_at"],
                "client"   => ["on", "sortable", "", "clients.name"],
                "summary"  => ["on", "sortable", "", "summary"],
                "invoiced" => ["on", "notSortable", "text-center", ""],
                "total"    => ["on", "sortable", "text-right pr-4", "quote_amounts.total"],
            ];
    }

    public static function clientColumnSettings()
    {
        return
            [
                "City"                             => "city",
                "State"                            => "state",
                "PostalCode"                       => "postal_code",
                "Country"                          => "country",
                "PhoneNumber"                      => "phone_number",
                "FaxNumber"                        => "fax_number",
                "MobileNumber"                     => "mobile_number",
                "WebAddress"                       => "web_address",
                "SocialMediaUrl"                   => "social_media_url",
                "LeadSource"                       => "lead_source",
                "LeadSourceNotes"                  => "lead_source_notes",
                "GeneralNotes"                     => "general_notes",
                "InvoicePrefix"                    => "invoice_prefix",
                "DefaultCurrency"                  => "default_currency",
                "Language"                         => "language",
                "AllowChildAccounts"               => "allow_child_accounts",
                "ThirdPartyBillPayer"              => "third_party_bill_payer",
                "Timezone"                         => "timezone",
                "AutomaticEmailPaymentReceipt"     => "automatic_email_payment_receipts",
                "AutomaticEmailOnRecurringInvoice" => "automatic_email_on_recur",
                "OnlinePaymentProcessingFee"       => "allow_online_payment_processing_fees",
            ];
    }

    public static function userDefaultSetting($choice = null)
    {
        $widgetColumnsWidth  = [
            "widgetColumnWidthSalesChart",
            "widgetColumnWidthTasks",
            "widgetColumnWidthOpenInvoiceAging",
            "widgetColumnWidthClientActivity",
            "widgetColumnWidthClientTimeLine",
        ];
        $widgetEnables       = [
            "widgetEnabledTasks",
            "widgetEnabledOpenInvoiceAging",
            "widgetEnabledSalesChart",
            "widgetEnabledClientActivity",
            "widgetEnabledClientTimeLine",
        ];
        $widgetOtherSettings = [
            "accumulateTotals",
            "includeTimeInTaskDueDate",
        ];
        $widgetDisplayOrders = [
            "widgetDisplayOrderTasks",
            'widgetDisplayOrderClientActivity',
            "widgetDisplayOrderSalesChart",
            "widgetDisplayOrderOpenInvoiceAging",
            "widgetDisplayOrderClientTimeLine",
        ];
        $widgetPositions     = [
            "widgetPositionRight",
            "widgetPositionLeft",
            "widgetPositionCenter",
        ];
        $widgetKpiCards      = [
            'dashboardDraftInvoices',
            'dashboardSentInvoices',
            'dashboardOverdueInvoices',
            'dashboardPaymentsCollectedInvoices',
            'dashboardDraftQuotes',
            'dashboardSentQuotes',
            'dashboardRejectedQuotes',
            'dashboardApprovedQuotes',
        ];

        if ($choice != null)
        {
            return $$choice;
        }
        else
        {
            return [
                'widgetColumnsWidth'  => $widgetColumnsWidth,
                'widgetEnables'       => $widgetEnables,
                'widgetDisplayOrders' => $widgetDisplayOrders,
                'widgetPositions'     => $widgetPositions,
                'widgetKpiCards'      => $widgetKpiCards,
                'widgetOtherSettings' => $widgetOtherSettings,
            ];
        }
    }
}