<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Clients\Models;

use FI\Modules\Currencies\Models\Currency;
use FI\Modules\CustomFields\Support\CustomFieldsParser;
use FI\Modules\Invoices\Models\InvoiceAmount;
use FI\Modules\Payments\Models\Payment;
use FI\Support\CurrencyFormatter;
use FI\Support\DateFormatter;
use FI\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Client extends Model
{
    use Sortable;

    protected $guarded = ['id', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected $sortable = ['id', 'name', 'email', 'phone', 'balance', 'active', 'custom', 'created_at'];

    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */

    public static function findByName($clientName)
    {
        return Client::where('name', $clientName)->first();
    }

    public static function getStatusList()
    {
        return [
            'active'   => trans('fi.active'),
            'inactive' => trans('fi.inactive'),
        ];
    }

    public static function getTypesList()
    {
        return [
            'lead'      => trans('fi.lead'),
            'prospect'  => trans('fi.prospect'),
            'customer'  => trans('fi.customer'),
            'affiliate' => trans('fi.affiliate'),
            'other'     => trans('fi.other'),
        ];
    }

    public static function getClientTitle()
    {
        return [
            'Mr.'   => trans('fi.mr'),
            'Miss'  => trans('fi.miss'),
            'Ms.'   => trans('fi.ms'),
            'Mrs.'  => trans('fi.mrs'),
            'Dr.'   => trans('fi.dr'),
            'Prof.' => trans('fi.prof'),
        ];
    }

    public static function getDropDownList()
    {
        return ['' => trans('fi.select_client')] + self::select(DB::raw("CASE WHEN email != '' THEN CONCAT(COALESCE(`name`,''),'<span> [',COALESCE(`email`,''),']</span>') ELSE `name` END AS client_name"), 'id')->whereActive(1)->orderBy('name')->pluck('client_name', 'id')->all();
    }

    public static function getParentClients($id = null)
    {
        return self::select(DB::raw("CASE WHEN email != '' THEN CONCAT(COALESCE(`name`,''),'<span> [',COALESCE(`email`,''),']</span>') ELSE `name` END AS client_name"), 'id')->where('id', '!=', $id)->whereAllowChildAccounts(1)->whereActive(1)->orderBy('name')->pluck('client_name', 'id')->all();
    }

    public static function getChildClients($id)
    {
        return self::select(DB::raw("CASE WHEN email != '' THEN CONCAT(COALESCE(`name`,''),'<span> [',COALESCE(`email`,''),']</span>') ELSE `name` END AS client_name"), 'id', 'active')->where('parent_client_id', $id)->orderBy('name')->get();
    }

    public static function getThirdPartyBillPayers($id)
    {
        return self::select(DB::raw("CASE WHEN email != '' THEN CONCAT(COALESCE(`name`,''),'<span> [',COALESCE(`email`,''),']</span>') ELSE `name` END AS client_name"), 'id', 'active')->where('invoices_paid_by', $id)->orderBy('name')->get();
    }

    public static function getList()
    {
        return self::orderBy('name')->pluck('name', 'id')->all();
    }

    public static function getClientListWithId()
    {
        return ['' => trans('fi.select_client')] + self::select('id', 'name')->whereActive(1)->orderBy('name')->pluck('name', 'id')->all();
    }

    public static function getInvoicesPaidByClients()
    {
        return self::select(DB::raw("CASE WHEN email != '' THEN CONCAT(COALESCE(`name`,''),'<span>[',COALESCE(`email`,''),']</span>') ELSE `name` END AS name"), 'id')->whereActive(1)->whereThirdPartyBillPayer(1)->orderBy('name')->pluck('name', 'id')->all();
    }

    public static function getClientList()
    {
        return self::pluck('name', 'id')->all();
    }

    public static function getHeaders()
    {
        return ['id', 'name', 'email_address', 'type', 'address', 'phone_number', 'created', 'balance', 'active'];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function attachments()
    {
        return $this->morphMany('FI\Modules\Attachments\Models\Attachment', 'attachable');
    }

    public function contacts()
    {
        return $this->hasMany('FI\Modules\Clients\Models\Contact');
    }

    public function currency()
    {
        return $this->belongsTo('FI\Modules\Currencies\Models\Currency', 'currency_code', 'code');
    }

    public function custom()
    {
        return $this->hasOne('FI\Modules\CustomFields\Models\ClientCustom');
    }

    public function expenses()
    {
        return $this->hasMany('FI\Modules\Expenses\Models\Expense');
    }

    public function invoices()
    {
        return $this->hasMany('FI\Modules\Invoices\Models\Invoice');
    }

    public function merchant()
    {
        return $this->hasOne('FI\Modules\Merchant\Models\MerchantClient');
    }

    public function notes()
    {
        return $this->morphMany('FI\Modules\Notes\Models\Note', 'notable');
    }

    public function quotes()
    {
        return $this->hasMany('FI\Modules\Quotes\Models\Quote');
    }

    public function recurringInvoices()
    {
        return $this->hasMany('FI\Modules\RecurringInvoices\Models\RecurringInvoice');
    }

    public function transitions()
    {
        return $this->morphMany('FI\Modules\Transitions\Models\Transitions', 'transitionable');
    }

    public function user()
    {
        return $this->hasOne('FI\Modules\Users\Models\User');
    }

    public function tags()
    {
        return $this->hasMany('FI\Modules\Clients\Models\ClientTag');
    }

    public function tasks()
    {
        return $this->hasMany('FI\Modules\TaskList\Models\Task');
    }

    public function parent()
    {
        return $this->belongsTo('FI\Modules\Clients\Models\Client', 'parent_client_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany('FI\Modules\Payments\Models\Payment');
    }

    public function containers()
    {
        return $this->hasMany('Addons\Containers\Models\Container');
    }

    public function clientLeadSource()
    {
        return $this->hasOne('FI\Modules\Tags\Models\Tag', 'id', 'lead_source_tag_id');
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

    public function getFormattedBalanceAttribute()
    {
        if ($this->unapplied_Payments != null and $this->unapplied_Payments != 0)
        {
            $this->balance = ($this->balance - $this->unapplied_Payments);
        }

        return CurrencyFormatter::format($this->balance, $this->currency);
    }

    public function getFormattedUpdatedAtAttribute()
    {
        return DateFormatter::format($this->attributes['updated_at']);
    }

    public function getFormattedWebAddressAttribute()
    {
        $explode = explode("://", $this->attributes['web']);
        return ($explode[0] == 'https') ? $this->attributes['web'] :
            (($this->attributes['web'] != null) ? 'http://' . preg_replace('#^.*://#', '', $this->attributes['web']) : '');
    }

    public function getFormattedSocialMediaUrlAttribute()
    {
        $explode = explode("://", $this->attributes['social_media_url']);
        return ($explode[0] == 'https') ? $this->attributes['social_media_url'] :
            (($this->attributes['social_media_url'] != null) ? 'http://' . preg_replace('#^.*://#', '', $this->attributes['social_media_url']) : '');
    }

    public function getFormattedCreatedAtAttribute()
    {
        return DateFormatter::format($this->attributes['created_at']);
    }

    public function getFormattedPaidAttribute()
    {
        return CurrencyFormatter::format($this->paid, $this->currency);
    }

    public function getFormattedTotalAttribute()
    {
        return CurrencyFormatter::format($this->total, $this->currency);
    }

    public function getFormattedAddressAttribute()
    {
        return nl2br(formatAddress($this));
    }

    public function getFormattedImportantNoteAttribute()
    {
        return nl2br($this->important_note);
    }

    public function getJsFormattedImportantNoteAttribute()
    {
        $patterns      = ["/\\\\/", '/\n/', '/\r/', '/\t/', '/\v/', '/\f/'];
        $replacements  = ['\\\\\\', '</br>', '\r', '\t', '\v', '\f'];
        $importantNote = preg_replace($patterns, $replacements, $this->important_note);
        return $importantNote;
    }

    public function getFormattedGeneralNotesAttribute()
    {
        return nl2br($this->general_notes);
    }

    public function getLocalTimeAttribute()
    {
        if ($this->timezone)
        {
            return DateFormatter::format(null, true, $this->timezone);
        }

        return trans('fi.unknown');
    }

    public function getClientEmailAttribute()
    {
        return $this->email;
    }

    public function getParentNameAttribute()
    {
        return ($this->parent) ? $this->parent->name : null;
    }

    public function getInvoicesPaidByNameAttribute()
    {
        if (!empty($this->invoices_paid_by))
        {
            return $this->whereId($this->invoices_paid_by)->first()->name;
        }
        return false;
    }

    public function getInvoicesPaidByEmailAttribute()
    {
        if (!empty($this->invoices_paid_by))
        {
            return $this->whereId($this->invoices_paid_by)->first()->email;
        }
        return false;
    }

    public function getShouldEmailPaymentReceiptAttribute()
    {
        if (!$this->email)
        {
            return false;
        }

        switch ($this->automatic_email_payment_receipt)
        {
            case 'yes':
                return true;
            case 'no':
                return false;
            case 'default':
                return config('fi.automaticEmailPaymentReceipts');
            default:
                return false;
        }
    }

    public function getAutomaticEmailOnRecur()
    {
        if (!$this->email)
        {
            return false;
        }

        switch ($this->attributes['automatic_email_on_recur'])
        {
            case 'yes':
                return true;
            case 'no':
                return false;
            case 'default':
                return config('fi.automaticEmailOnRecur');
            default:
                return false;
        }
    }

    public function deleteTags(Client $client)
    {
        $client->tags()->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeGetSelect()
    {
        return self::select('clients.*',
            DB::raw('(' . $this->getBalanceSql() . ') as balance'),
            DB::raw('(' . $this->getPaidSql() . ') AS paid'),
            DB::raw('(' . $this->getTotalSql() . ') AS total'),
            DB::raw("(SELECT SUM(" . DB::getTablePrefix() . "payments.remaining_balance) FROM " . DB::getTablePrefix() . "payments
                              WHERE " . DB::getTablePrefix() . "payments.client_id = " . DB::getTablePrefix() . "clients.id AND " . DB::getTablePrefix() . "payments.remaining_balance > 0) as unapplied_Payments"
            )
        );
    }

    public function scopeStatus($query, $status)
    {
        if ($status == 'active')
        {
            $query->where('active', 1);
        }
        else if ($status == 'inactive')
        {
            $query->where('active', 0);
        }

        return $query;
    }

    public function scopeType($query, $type)
    {
        if ($type)
        {
            $query->where('type', $type);
        }

        return $query;
    }

    public function scopeFieldsWiseSearch($query, $fieldsWiseSearch, $operator)
    {
        if ($fieldsWiseSearch != null)
        {
            $query->where(function ($query) use ($fieldsWiseSearch, $operator) {
                foreach ($fieldsWiseSearch as $key => $value)
                {
                    if (substr($value, 0, 2) == '!=')
                    {
                        $query->whereNotIn($key, [substr($value, 2)]);
                    }
                    else
                    {
                        if ($operator == 'or')
                        {
                            if (substr_count($key, '->') == 0)
                            {
                                $query->orWhere('clients.' . $key, 'like', '%' . $value . '%');
                            }
                            else
                            {
                                $tableAndField = explode('->', $key);

                                $query->whereHas($tableAndField[0], function ($query) use ($value, $tableAndField) {
                                    $query->orWhere($tableAndField[1], 'like', '%' . $value . '%');
                                });
                            }
                        }
                        if ($operator == 'and')
                        {

                            if (substr_count($key, '->') == 0)
                            {
                                $query->where('clients.' . $key, 'like', '%' . $value . '%');
                            }
                            else
                            {
                                $tableAndField = explode('->', $key);

                                $query->whereHas($tableAndField[0], function ($query) use ($value, $tableAndField) {
                                    $query->where($tableAndField[1], 'like', '%' . $value . '%');
                                });
                            }
                        }
                    }
                }
            });
        }
        return $query;
    }

    public function scopeKeywords($query, $keywords)
    {
        if ($keywords)
        {
            $keywords = explode(' ', $keywords);

            if ($keywords)
            {
                $query->where(function ($query) use ($keywords) {
                    foreach ($keywords as $keyword)
                    {
                        // Must match all keywords
                        $query->WhereRaw("CONCAT_WS('^',LOWER(name),LOWER(email),vat_tax_id,phone,mobile,lower(address),lower(city),zip,lower(general_notes)) LIKE ?", ['%' . $keyword . '%']);
                    }

                    // Separate the OR portion of the keyword match for ID check.
                    foreach ($keywords as $keyword)
                    {
                        $query->orWhere('id', $keyword);
                    }
                });
            }
        }

        return $query;
    }

    public function scopeTags($query, $tags, $tagsMustMatchAll)
    {
        if (!empty($tags))
        {
            if ($tagsMustMatchAll)
            {
                $query->whereHas('tags', function ($query) use ($tags) {
                    $query->whereIn("tag_id", $tags);

                }, "=", count($tags));

            }
            else
            {
                $query->whereHas('tags', function ($query) use ($tags) {
                    $query->whereIn("tag_id", $tags);

                });
            }
        }

        return $query;
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
    | Subqueries
    |--------------------------------------------------------------------------
    */

    private function getBalanceSql()
    {
        return DB::table('invoice_amounts')->select(DB::raw('sum(balance)'))->whereIn('invoice_id', function ($q) {
            $q->select('id')
                ->from('invoices')
                ->where('invoices.client_id', '=', DB::raw(DB::getTablePrefix() . 'clients.id'))
                ->whereRaw(DB::getTablePrefix() . "invoices.status <> 'canceled'");
        })->toSql();
    }

    private function getPaidSql()
    {
        return DB::table('invoice_amounts')->select(DB::raw('sum(paid)'))->whereIn('invoice_id', function ($q) {
            $q->select('id')
                ->from('invoices')
                ->where('invoices.client_id', '=', DB::raw(DB::getTablePrefix() . 'clients.id'));
        })->toSql();
    }

    private function getTotalSql()
    {
        return DB::table('invoice_amounts')->select(DB::raw('sum(total)'))->whereIn('invoice_id', function ($q) {
            $q->select('id')
                ->from('invoices')
                ->where('invoices.client_id', '=', DB::raw(DB::getTablePrefix() . 'clients.id'));
        })->toSql();
    }

    /*
    |--------------------------------------------------------------------------
    | Other
    |--------------------------------------------------------------------------
    */

    public function customField($label, $rawHtml = true)
    {
        $customField = config('fi.customFields')->where('tbl_name', 'clients')->where('field_label', $label)->first();

        if ($customField)
        {
            return CustomFieldsParser::getFieldValue($this->custom, $customField, $rawHtml);
        }

        return null;

    }

    public function currencyWiseSummary()
    {
        $totalInvoiced          = $this->currencyWiseTotalInvoiced();
        $totalPaidInvoices      = $this->currencyWiseTotalPaidInvoices();
        $totalOpenInvoices      = $this->currencyWiseTotalOpenInvoices();
        $totalOpenCredits       = $this->currencyWiseTotalOpenCredits();
        $totalUnappliedPayments = $this->currencyWiseUnappliedPayments();
        $totalBalance           = $result = [];

        $currencies = array_unique(array_merge(
            array_keys($totalInvoiced),
            array_keys($totalPaidInvoices),
            array_keys($totalOpenInvoices),
            array_keys($totalOpenCredits),
            array_keys($totalUnappliedPayments)
        ));
        $currencies = array_combine($currencies, $currencies);
        foreach ($currencies as $currencyCode)
        {
            $currencyObject = Currency::getByCode($currencyCode);

            $amount                      = $totalOpenInvoices[$currencyCode] ?? 0;
            $openCredit                  = $totalOpenCredits[$currencyCode] ?? 0;
            $unappliedPayments           = $totalUnappliedPayments[$currencyCode] ?? 0;
            $balance                     = ($amount - (abs($openCredit) + $unappliedPayments));
            $totalBalance[$currencyCode] = CurrencyFormatter::format($balance, $currencyObject);


            if (isset($totalInvoiced[$currencyCode]) && !empty($totalInvoiced[$currencyCode]))
            {
                $totalInvoiced[$currencyCode] = CurrencyFormatter::format($totalInvoiced[$currencyCode], $currencyObject);
            }
            if (isset($totalPaidInvoices[$currencyCode]) && !empty($totalPaidInvoices[$currencyCode]))
            {
                $totalPaidInvoices[$currencyCode] = CurrencyFormatter::format($totalPaidInvoices[$currencyCode], $currencyObject);
            }
            if (isset($totalOpenInvoices[$currencyCode]) && !empty($totalOpenInvoices[$currencyCode]))
            {
                $totalOpenInvoices[$currencyCode] = CurrencyFormatter::format($totalOpenInvoices[$currencyCode], $currencyObject);
            }
            if (isset($totalOpenCredits[$currencyCode]) && !empty($totalOpenCredits[$currencyCode]))
            {
                $totalOpenCredits[$currencyCode] = CurrencyFormatter::format($totalOpenCredits[$currencyCode], $currencyObject);
            }
            if (isset($totalUnappliedPayments[$currencyCode]) && !empty($totalUnappliedPayments[$currencyCode]))
            {
                $totalUnappliedPayments[$currencyCode] = CurrencyFormatter::format($totalUnappliedPayments[$currencyCode], $currencyObject);
            }
        }
        foreach ($currencies as $currencyCode)
        {
            $result[$currencyCode]['totalInvoiced']          = $totalInvoiced[$currencyCode] ?? '';
            $result[$currencyCode]['totalPaidInvoices']      = $totalPaidInvoices[$currencyCode] ?? '';
            $result[$currencyCode]['totalOpenInvoices']      = $totalOpenInvoices[$currencyCode] ?? '';
            $result[$currencyCode]['totalOpenCredits']       = $totalOpenCredits[$currencyCode] ?? '';
            $result[$currencyCode]['totalUnappliedPayments'] = $totalUnappliedPayments[$currencyCode] ?? '';
            $result[$currencyCode]['totalBalance']           = $totalBalance[$currencyCode] ?? '';
        }
        return $result;
    }

    public function currencyWiseTotalInvoiced()
    {
        return InvoiceAmount::join('invoices', 'invoice_amounts.invoice_id', '=', 'invoices.id')
            ->where([
                ['invoices.client_id', '=', $this->id],
                ['invoices.status', '!=', 'canceled'],
                ['invoices.type', '=', 'invoice'],
            ])
            ->whereNotNull('invoices.currency_code')
            ->groupBy('currency_code')
            ->selectRaw('sum(total) as total_invoiced, currency_code')
            ->pluck('total_invoiced', 'currency_code')
            ->toArray();
    }

    public function currencyWiseTotalPaidInvoices()
    {
        return InvoiceAmount::join('invoices', 'invoice_amounts.invoice_id', '=', 'invoices.id')
            ->where([
                ['invoices.client_id', '=', $this->id],
                ['invoices.status', '!=', 'canceled'],
                ['invoices.type', '=', 'invoice'],
            ])
            ->whereNotNull('invoices.currency_code')
            ->groupBy('currency_code')
            ->selectRaw('sum(paid) as total_paid, currency_code')
            ->pluck('total_paid', 'currency_code')
            ->toArray();
    }

    public function currencyWiseTotalOpenInvoices()
    {
        return InvoiceAmount::join('invoices', 'invoice_amounts.invoice_id', '=', 'invoices.id')
            ->where([
                ['invoices.client_id', '=', $this->id],
                ['invoices.status', '!=', 'canceled'],
                ['invoices.type', '=', 'invoice'],
            ])
            ->whereNotNull('invoices.currency_code')
            ->groupBy('currency_code')
            ->selectRaw('sum(balance) as total_open_invoices, currency_code')
            ->pluck('total_open_invoices', 'currency_code')
            ->toArray();
    }

    public function currencyWiseTotalOpenCredits()
    {
        return InvoiceAmount::join('invoices', 'invoice_amounts.invoice_id', '=', 'invoices.id')
            ->where([
                ['invoices.client_id', '=', $this->id],
                ['invoices.status', '!=', 'canceled'],
                ['invoices.type', '=', 'credit_memo'],
            ])
            ->whereNotNull('invoices.currency_code')
            ->groupBy('currency_code')
            ->selectRaw('sum(balance) as total_open_credits, currency_code')
            ->pluck('total_open_credits', 'currency_code')
            ->toArray();
    }

    public function currencyWiseUnappliedPayments()
    {
        return Payment::whereClientId($this->id)
            ->whereNotNull('currency_code')
            ->groupBy('currency_code')
            ->selectRaw('sum(remaining_balance) as total_unapplied_payments, currency_code')
            ->pluck('total_unapplied_payments', 'currency_code')
            ->toArray();
    }
}