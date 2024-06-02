<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Invoices\Models;

use Carbon\Carbon;
use FI\Modules\CustomFields\Support\CustomFieldsParser;
use FI\Modules\Payments\Models\PaymentInvoice;
use FI\Support\CurrencyFormatter;
use FI\Support\DateFormatter;
use FI\Support\FileNames;
use FI\Support\HTML;
use FI\Support\NumberFormatter;
use FI\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use stdClass;

class Invoice extends Model
{
    use Sortable;

    protected $guarded = ['id'];

    protected $sortable = [
        'number' => ['LENGTH(number)', 'number'],
        'invoice_date',
        'due_at',
        'clients.name',
        'summary',
        'invoice_amounts.total',
        'invoice_amounts.balance',
        'invoice_amounts.tax',
        'invoice_amounts.subtotal',
    ];

    protected $dates = ['due_at', 'invoice_date'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function activities()
    {
        return $this->morphMany('FI\Modules\Activity\Models\Activity', 'audit');
    }

    public function amount()
    {
        return $this->hasOne('FI\Modules\Invoices\Models\InvoiceAmount');
    }

    public function attachments()
    {
        return $this->morphMany('FI\Modules\Attachments\Models\Attachment', 'attachable');
    }

    public function client()
    {
        return $this->belongsTo('FI\Modules\Clients\Models\Client');
    }

    public function tags()
    {
        return $this->hasMany('FI\Modules\Invoices\Models\InvoiceTag');
    }

    public function clientAttachments()
    {
        $relationship = $this->morphMany('FI\Modules\Attachments\Models\Attachment', 'attachable');

        if ($this->amount->balance == 0 && $this->amount->total > 0 && $this->attributes['status'] != 'canceled')
        {
            $relationship->whereIn('client_visibility', [1, 2]);
        }
        else
        {
            $relationship->where('client_visibility', 1);
        }

        return $relationship;
    }

    public function companyProfile()
    {
        return $this->belongsTo('FI\Modules\CompanyProfiles\Models\CompanyProfile');
    }

    public function currency()
    {
        return $this->belongsTo('FI\Modules\Currencies\Models\Currency', 'currency_code', 'code');
    }

    public function custom()
    {
        return $this->hasOne('FI\Modules\CustomFields\Models\InvoiceCustom');
    }

    public function documentNumberScheme()
    {
        return $this->belongsTo('FI\Modules\DocumentNumberSchemes\Models\DocumentNumberScheme');
    }

    public function items()
    {
        return $this->hasMany('FI\Modules\Invoices\Models\InvoiceItem')
            ->orderBy('display_order');
    }

    public function commissions()
    {
        return $this->hasManyThrough('Addons\Commission\Models\InvoiceItemCommission', 'FI\Modules\Invoices\Models\InvoiceItem');
    }

    // This and items() are the exact same. This is added to appease the IDE gods
    // and the fact that Laravel has a protected items property.
    public function invoiceItems()
    {
        return $this->hasMany('FI\Modules\Invoices\Models\InvoiceItem')
            ->orderBy('display_order');
    }

    public function mailQueue()
    {
        return $this->morphMany('FI\Modules\MailQueue\Models\MailQueue', 'mailable');
    }

    public function notes()
    {
        return $this->morphMany('FI\Modules\Notes\Models\Note', 'notable');
    }

    public function payments()
    {
        return $this->hasMany('FI\Modules\Payments\Models\PaymentInvoice');
    }

    public function quote()
    {
        return $this->hasOne('FI\Modules\Quotes\Models\Quote');
    }

    public function transitions()
    {
        return $this->morphMany('FI\Modules\Transitions\Models\Transitions', 'transitionable');
    }

    public function transactions()
    {
        return $this->hasMany('FI\Modules\Merchant\Models\InvoiceTransaction');
    }

    public function user()
    {
        return $this->belongsTo('FI\Modules\Users\Models\User');
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
            '2' => trans('fi.visible_after_payment'),
        ];
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->formatted_invoice_date;
    }

    public function getFormattedInvoiceDateAttribute()
    {
        return DateFormatter::format($this->attributes['invoice_date']);
    }

    public function getFormattedDateEmailedAttribute()
    {
        if ($this->attributes['date_emailed'] != null)
        {
            return DateFormatter::format($this->attributes['date_emailed']);
        }
    }

    public function getFormattedDateMailedAttribute()
    {
        if ($this->attributes['date_mailed'] != null)
        {
            return DateFormatter::format($this->attributes['date_mailed']);
        }
    }

    public function getFormattedUpdatedAtAttribute()
    {
        return DateFormatter::format($this->attributes['updated_at']);
    }

    public function getFormattedDueAtAttribute()
    {
        return DateFormatter::format($this->attributes['due_at']);
    }

    public function getDueAt($format)
    {
        return Carbon::parse($this->attributes['due_at'])->format($format);
    }

    public function getFormattedTermsAttribute()
    {
        return nl2br($this->attributes['terms']);
    }

    public function getFormattedFooterAttribute()
    {
        return nl2br($this->attributes['footer']);
    }

    public function getPaidStatusAttribute()
    {
        if ($this->amount->balance == 0 && $this->amount->total > 0 && $this->attributes['status'] != 'canceled')
        {
            return true;
        }
        return false;
    }

    public function getUnPaidStatusAttribute()
    {
        if (in_array($this->attributes['status'], ['sent', 'draft']) && $this->amount->balance > 0 && $this->amount->total > 0)
        {
            return true;
        }
        return false;
    }

    public function getOverdueStatusAttribute()
    {
        $status = (config('fi.includeDraftInvoicesUnpaidAndOverdue') == 1) ? (in_array($this->attributes['status'], ['sent', 'draft'])) : ($this->attributes['status'] == 'sent');

        if ($this->amount->balance > 0 && $this->amount->total > 0 && $this->attributes['due_at'] < date('Y-m-d') and $status)
        {
            return true;
        }
        return false;
    }

    public function getVirtualStatusAttribute()
    {
        $virtual_status                 = [];
        $overdueUnpaidPrimaryStatusList = (config('fi.includeDraftInvoicesUnpaidAndOverdue') == 1) ? ['sent', 'draft'] : ['sent'];

        if (request('status') != null && request('status') != 'all')
        {
            if (in_array(request('status'), ['draft', 'sent', 'canceled']) != true)
            {

                $virtual_status[] = request('status');
            }
        }
        if ($this->amount->balance == 0 && $this->amount->total > 0 && $this->attributes['status'] != 'canceled')
        {
            $virtual_status[] = 'paid';
        }

        if ($this->amount->balance > 0 && $this->amount->total > 0 && $this->attributes['due_at'] < date('Y-m-d') and
            in_array($this->attributes['status'], $overdueUnpaidPrimaryStatusList))
        {
            $virtual_status[] = 'overdue';
        }

        if ($this->amount->balance > 0 && $this->amount->total > 0 and in_array($this->attributes['status'], $overdueUnpaidPrimaryStatusList))
        {
            $virtual_status[] = 'unpaid';
        }

        if ($this->amount->balance > 0 && $this->amount->total > 0 && $this->attributes['date_mailed'] != null && $this->attributes['status'] == 'sent')
        {
            $virtual_status[] = 'mailed';
        }

        if ($this->amount->balance > 0 && $this->amount->total > 0 && $this->attributes['date_emailed'] != null && $this->attributes['status'] == 'sent')
        {
            $virtual_status[] = 'emailed';
        }

        if (array_intersect(['overdue', 'unpaid'], $virtual_status) == ['overdue', 'unpaid'])
        {
            if (($key = array_search('unpaid', $virtual_status)) !== false)
            {
                unset($virtual_status[$key]);
            }
        }
        if (array_intersect(['paid'], $virtual_status) == ['paid'])
        {
            return $virtual_status = ['paid'];
        }

        return array_unique($virtual_status);
    }

    public function getStatusTextAttribute()
    {
        return $this->attributes['status'];
    }

    public function getIsOverdueAttribute()
    {
        $status = (config('fi.includeDraftInvoicesUnpaidAndOverdue') == 1) ? (in_array($this->attributes['status'], ['sent', 'draft'])) : ($this->attributes['status'] == 'sent');

        if ($this->amount->balance > 0 && $this->amount->total > 0 && $this->attributes['due_at'] < Carbon::now()->format('Y-m-d') && $status)
        {
            return 1;
        }

        return 0;
    }

    public function getPublicUrlAttribute()
    {
        return route('clientCenter.public.invoice.show', [$this->url_key, $this->token]);
    }

    public function getIsForeignCurrencyAttribute()
    {
        if ($this->attributes['currency_code'] == config('fi.baseCurrency'))
        {
            return false;
        }

        return true;
    }

    public function getFormattedTagsAttribute()
    {
        $invoiceTags = [];

        foreach ($this->tags as $tag)
        {
            $invoiceTags[] = $tag->tag->name;
        }

        if (empty($invoiceTags))
        {
            return '';
        }
        else
        {
            if (count($invoiceTags) == 1)
            {
                return $invoiceTags[0];
            }
            else if (count($invoiceTags) == 2)
            {
                return $invoiceTags[0] . ', ' . $invoiceTags[1];
            }
            else
            {
                return $invoiceTags[0] . ', ' . $invoiceTags[1] . '..';
            }

        }

    }

    public function getHtmlAttribute()
    {
        return HTML::invoice($this);
    }

    public function getPdfFilenameAttribute()
    {
        return FileNames::invoice($this);
    }

    public function getFormattedNumericDiscountAttribute()
    {
        return NumberFormatter::format($this->attributes['discount']);
    }

    public function getIsPayableAttribute()
    {
        return $this->status_text <> 'canceled' && $this->amount->balance > 0 && $this->type <> 'credit_memo';
    }

    public function getIsApplicableAttribute()
    {
        return (($this->type == 'credit_memo') && (abs($this->amount->balance) > 0) && (!in_array($this->status_text, ['canceled', 'applied'])));
    }

    public function getTokenAttribute()
    {
        return ((config('fi.secure_link')) == 1) ? Crypt::encrypt(Carbon::now()->addDay(config('fi.secure_link_expire_day'))->format('Y-m-d')) : null;
    }

    /**
     * Gathers a summary of both invoice and item taxes to be displayed on invoice.
     *
     * @return array
     */
    public function getSummarizedTaxesAttribute()
    {
        $taxes = [];

        foreach ($this->items as $item)
        {
            if ($item->taxRate)
            {
                $key = $item->taxRate->name;

                if (!isset($taxes[$key]))
                {
                    $taxes[$key]                   = new stdClass();
                    $taxes[$key]->name             = $item->taxRate->name;
                    $taxes[$key]->percent          = $item->taxRate->formatted_percent;
                    $taxes[$key]->total            = $item->amount->tax_1;
                    $taxes[$key]->raw_percent      = $item->taxRate->percent;
                    $taxes[$key]->unformated_total = $item->amount->tax_1;
                }
                else
                {
                    $taxes[$key]->total            += $item->amount->tax_1;
                    $taxes[$key]->unformated_total += $item->amount->tax_1;
                }
            }

            if ($item->taxRate2)
            {
                $key = $item->taxRate2->name;

                if (!isset($taxes[$key]))
                {
                    $taxes[$key]                   = new stdClass();
                    $taxes[$key]->name             = $item->taxRate2->name;
                    $taxes[$key]->percent          = $item->taxRate2->formatted_percent;
                    $taxes[$key]->total            = $item->amount->tax_2;
                    $taxes[$key]->raw_percent      = $item->taxRate2->percent;
                    $taxes[$key]->unformated_total = $item->amount->tax_2;
                }
                else
                {
                    $taxes[$key]->total            += $item->amount->tax_2;
                    $taxes[$key]->unformated_total += $item->amount->tax_2;
                }
            }
        }

        foreach ($taxes as $key => $tax)
        {
            $taxes[$key]->total = CurrencyFormatter::format($tax->total, $this->currency);
        }

        return $taxes;
    }

    public function getFormattedTotalConvenienceChargesAttribute()
    {
        return CurrencyFormatter::format($this->attributes['total_convenience_charges'], $this->currency);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeClient($query, $clientId = null)
    {
        if ($clientId)
        {
            $query->where('client_id', $clientId);
        }

        return $query;
    }

    public function scopeDraft($query)
    {
        return $query->where('status', '=', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', '=', 'sent');
    }

    public function scopeMailed($query)
    {
        return $query->where(function ($q) {
            $q->where('invoice_amounts.balance', '>', 0)
                ->where('invoice_amounts.total', '>', 0);
        })->where(function ($q) {
            $q->whereNotNull('invoices.date_mailed')->where('invoices.status', '=', 'sent');
        });
    }

    public function scopeEmailed($query)
    {
        return $query->where(function ($q) {
            $q->where('invoice_amounts.balance', '>', 0)
                ->where('invoice_amounts.total', '>', 0);
        })->where(function ($q) {
            $q->whereNotNull('invoices.date_emailed')->where('invoices.status', '=', 'sent');
        });
    }

    public function scopeSentOrMailed($query)
    {
        $query->mailed()->orWhere('status', '=', 'sent');
    }

    public function scopeAllButDraftAndCancelled($query)
    {
        return $query->whereNotIn('status', ['draft', 'cancelled']);
    }

    public function scopePaid($query)
    {
        return $query->where(function ($q) {
            $q->where('invoice_amounts.balance', '==', 0)
                ->where('invoice_amounts.total', '>', 0);
        })->where('status', '<>', 'canceled');
    }

    public function scopeCanceled($query)
    {
        return $query->where('status', '=', 'canceled');
    }

    public function scopeCompanyProfileId($query, $companyProfileId)
    {
        if ($companyProfileId)
        {
            $query->where('company_profile_id', $companyProfileId);
        }

        return $query;
    }

    public function scopeNotCanceled($query)
    {
        return $query->where('status', '<>', 'canceled');
    }

    public function scopeStatusIn($query, $statuses)
    {
        return $query->whereIn('status', $statuses);
    }

    public function scopeType($query, $type = 'invoice')
    {
        switch ($type)
        {
            case 'credit_memo':
                $query->where('type', 'credit_memo');
                break;
            default:
                $query->where('type', 'invoice');
                break;
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

    public function scopeFieldsWiseSearch($query, $fieldsWiseSearch)
    {
        if ($fieldsWiseSearch != null)
        {
            $query->where(function ($query) use ($fieldsWiseSearch)
            {
                // Separate the OR portion of the keyword match for ID check.
                foreach ($fieldsWiseSearch as $key => $value)
                {
                    if (substr_count($key, '->') == 0)
                    {
                        $query->orWhere('invoices.' . $key, 'like', '%' . $value . '%');
                    }
                    else
                    {
                        $tableAndField = explode('->', $key);

                        $query->whereHas($tableAndField[0], function ($query) use ($value, $tableAndField)
                        {
                            $query->where($tableAndField[1], 'like', '%' . $value . '%');
                        });
                    }

                }
            });
        }
        return $query;
    }

    public function scopeStatus($query, $status = null)
    {
        switch ($status)
        {
            case 'draft':
                $query->draft();
                break;
            case 'sent':
                $query->sent();
                break;
            case 'canceled':
                $query->canceled();
                break;
            case 'viewed':
                $query->viewed();
                break;
            case 'paid':
                $query->paid();
                break;
            case 'overdue':
                $query->overdue();
                break;
            case 'mailed':
                $query->mailed();
                break;
            case 'emailed':
                $query->emailed();
                break;
            case 'unpaid':
                $query->unpaid();
                break;
        }

        return $query;
    }

    public function scopeViewed($query)
    {
        return $query->whereViewed(1);
    }

    public function scopeUnpaid($query)
    {
        $overdueUnpaidPrimaryStatusList = (config('fi.includeDraftInvoicesUnpaidAndOverdue') == 1) ? ['sent', 'draft'] : ['sent'];

        return $query->where(function ($q) {
            $q->where('invoice_amounts.balance', '>', '0')
                ->where('invoice_amounts.total', '>', '0');
        })->whereIn('status', $overdueUnpaidPrimaryStatusList);
    }

    public function scopeOverdue($query)
    {
        $overdueUnpaidPrimaryStatusList = (config('fi.includeDraftInvoicesUnpaidAndOverdue') == 1) ? ['sent', 'draft'] : ['sent'];

        return $query->where(function ($q) {
            $q->where('invoice_amounts.balance', '>', '0')
                ->where('invoice_amounts.total', '>', '0');
        })->whereIn('status', $overdueUnpaidPrimaryStatusList)->where('due_at', '<', Carbon::now()->format('Y-m-d'));
    }

    public function scopeThisYear($query)
    {
        return $query->where(DB::raw('YEAR(invoice_date)'), '=', DB::raw('YEAR(CURRENT_DATE())'));
    }

    public function scopeThisMonth($query)
    {
        return $query->where(DB::raw('MONTH(invoice_date)'), '=', DB::raw('MONTH(CURRENT_DATE())'))
            ->where(DB::raw('YEAR(invoice_date)'), '=', DB::raw('YEAR(CURRENT_DATE())'));
    }

    public function scopeThisQuarter($query)
    {
        return $query->where('invoice_date', '>=', Carbon::now()->firstOfQuarter())
            ->where('invoice_date', '<=', Carbon::now()->lastOfQuarter());
    }

    public function scopeLastMonth($query)
    {
        return $query->where(DB::raw('MONTH(invoice_date)'), '=', DB::raw('MONTH(CURRENT_DATE - INTERVAL 1 MONTH)'))
            ->where(DB::raw('YEAR(invoice_date)'), '=', DB::raw('YEAR(CURRENT_DATE - INTERVAL 1 MONTH)'));
    }

    public function scopeLastQuarter($query)
    {
        return $query->where('invoice_date', '>=', Carbon::now()->subQuarters(1)->firstOfQuarter())
            ->where('invoice_date', '<=', Carbon::now()->subQuarters(1)->lastOfQuarter());
    }

    public function scopeLastYear($query)
    {
        return $query->where('invoice_date', '>=', Carbon::now()->subYears(1)->firstOfYear())
            ->where('invoice_date', '<=', Carbon::now()->subYears(1)->lastOfYear());
    }

    public function scopeDateRange($query, $fromDate = null, $toDate = null)
    {
        if (isset($fromDate) && isset($toDate) && $fromDate != null && $toDate != null)
        {
            return $query->where('invoices.invoice_date', '>=', $fromDate)
                ->where('invoices.invoice_date', '<=', $toDate);
        }
    }

    public function scopeToday($query)
    {
        return $query->where('invoices.invoice_date', '=', Carbon::now()->format('Y-m-d'));
    }

    public function scopeYesterday($query)
    {
        return $query->where('invoices.invoice_date', '=', Carbon::yesterday()->format('Y-m-d'));
    }

    public function scopeLast7Days($query)
    {
        return $query->where('invoices.invoice_date', '>=', Carbon::now()->subDays(6)->format('Y-m-d'))
            ->where('invoices.invoice_date', '<=', Carbon::now()->format('Y-m-d'));
    }

    public function scopeLast30Days($query)
    {
        return $query->where('invoices.invoice_date', '>=', Carbon::now()->subDays(29)->format('Y-m-d'))
            ->where('invoices.invoice_date', '<=', Carbon::now()->format('Y-m-d'));
    }

    public function scopeFirstQuarter($query)
    {

        $currentDate = (Carbon::createFromDate(Carbon::now()->format('Y'))->firstOfYear()->format('Y-m-d'));
        return $query->where('invoice_date', '>=', Carbon::createFromDate($currentDate)->addQuarter(0)->startOf('quarter')->format('Y-m-d'))
            ->where('invoice_date', '<=', Carbon::createFromDate($currentDate)->addQuarter(0)->endOf('quarter')->format('Y-m-d'));
    }

    public function scopeSecondQuarter($query)
    {
        $currentDate = (Carbon::createFromDate(Carbon::now()->format('Y'))->firstOfYear()->format('Y-m-d'));
        return $query->where('invoice_date', '>=', Carbon::createFromDate($currentDate)->addQuarter(1)->startOf('quarter')->format('Y-m-d'))
            ->where('invoice_date', '<=', Carbon::createFromDate($currentDate)->addQuarter(1)->endOf('quarter')->format('Y-m-d'));
    }

    public function scopeThirdQuarter($query)
    {
        $currentDate = (Carbon::createFromDate(Carbon::now()->format('Y'))->firstOfYear()->format('Y-m-d'));
        return $query->where('invoice_date', '>=', Carbon::createFromDate($currentDate)->addQuarter(2)->startOf('quarter')->format('Y-m-d'))
            ->where('invoice_date', '<=', Carbon::createFromDate($currentDate)->addQuarter(2)->endOf('quarter')->format('Y-m-d'));
    }

    public function scopeFourthQuarter($query)
    {
        $currentDate = (Carbon::createFromDate(Carbon::now()->format('Y'))->firstOfYear()->format('Y-m-d'));
        return $query->where('invoice_date', '>=', Carbon::createFromDate($currentDate)->addQuarter(3)->startOf('quarter')->format('Y-m-d'))
            ->where('invoice_date', '<=', Carbon::createFromDate($currentDate)->addQuarter(3)->endOf('quarter')->format('Y-m-d'));
    }

    public function scopeIsApplicable($query, $status = null)
    {
        if ($status == 'yes')
        {
            $query = $query->whereHas('amount', function ($query) {
                $query->where('invoice_amounts.balance', '<', 0)
                    ->where('invoice_amounts.total', '<>', 0)
                    ->where('invoices.type', '==', 'credit_memo')
                    ->whereNotIn('invoices.status', ['canceled', 'applied']);
            });
        }
        elseif ($status == 'no')
        {
            $query = $query->whereHas('amount', function ($query) {
                $query->where('invoice_amounts.balance', '=', '0.0000')
                    ->where('invoice_amounts.total', '<>', 0)
                    ->where('invoices.type', '==', 'credit_memo')
                    ->whereNot('invoices.status', '==', 'canceled');
            });
        }

        return $query;
    }

    //----------- Overdue ------------

    public function scopeThisYearOverdue($query)
    {
        return $query->where(DB::raw('YEAR(due_at)'), '=', DB::raw('YEAR(CURRENT_DATE())'));
    }

    public function scopeThisMonthOverdue($query)
    {
        return $query->where(DB::raw('MONTH(due_at)'), '=', DB::raw('MONTH(CURRENT_DATE())'))
            ->where(DB::raw('YEAR(due_at)'), '=', DB::raw('YEAR(CURRENT_DATE())'));
    }

    public function scopeThisQuarterOverdue($query)
    {
        return $query->where('due_at', '>=', Carbon::now()->firstOfQuarter())
            ->where('due_at', '<=', Carbon::now()->lastOfQuarter());
    }

    public function scopeLastMonthOverdue($query)
    {
        return $query->where(DB::raw('due_at'), '>=', Carbon::now()->subMonths(1)->firstOfMonth())
            ->where(DB::raw('due_at'), '<=', Carbon::now()->subMonths(1)->lastOfMonth());
    }

    public function scopeLastQuarterOverdue($query)
    {
        return $query->where('due_at', '>=', Carbon::now()->subQuarters(1)->firstOfQuarter())
            ->where('due_at', '<=', Carbon::now()->subQuarters(1)->lastOfQuarter());
    }

    public function scopeLastYearOverdue($query)
    {
        return $query->where('due_at', '>=', Carbon::now()->subYears(1)->firstOfYear())
            ->where('due_at', '<=', Carbon::now()->subYears(1)->lastOfYear());
    }

    public function scopeDateRangeOverdue($query, $fromDate, $toDate)
    {
        return $query->where('due_at', '>=', $fromDate)
            ->where('due_at', '<=', $toDate);
    }

    public function scopeTodayOverdue($query)
    {
        return $query->where('due_at', '=', Carbon::now()->format('Y-m-d'));
    }

    public function scopeYesterdayOverdue($query)
    {
        return $query->where('due_at', '=', Carbon::yesterday()->format('Y-m-d'));
    }

    public function scopeLast7DaysOverdue($query)
    {
        return $query->where('due_at', '>=', Carbon::now()->subDays(6)->format('Y-m-d'))
            ->where('due_at', '<=', Carbon::now()->format('Y-m-d'));
    }

    public function scopeLast30DaysOverdue($query)
    {
        return $query->where('due_at', '>=', Carbon::now()->subDays(29)->format('Y-m-d'))
            ->where('due_at', '<=', Carbon::now()->format('Y-m-d'));
    }

    public function scopeFirstQuarterOverdue($query)
    {
        $currentDate = (Carbon::createFromDate(Carbon::now()->format('Y'))->firstOfYear()->format('Y-m-d'));
        return $query->where('due_at', '>=', Carbon::createFromDate($currentDate)->addQuarter(0)->startOf('quarter')->format('Y-m-d'))
            ->where('due_at', '<=', Carbon::createFromDate($currentDate)->addQuarter(0)->endOf('quarter')->format('Y-m-d'));

    }

    public function scopeSecondQuarterOverdue($query)
    {
        $currentDate = (Carbon::createFromDate(Carbon::now()->format('Y'))->firstOfYear()->format('Y-m-d'));
        return $query->where('due_at', '>=', Carbon::createFromDate($currentDate)->addQuarter(1)->startOf('quarter')->format('Y-m-d'))
            ->where('due_at', '<=', Carbon::createFromDate($currentDate)->addQuarter(1)->endOf('quarter')->format('Y-m-d'));

    }

    public function scopeThirdQuarterOverdue($query)
    {
        $currentDate = (Carbon::createFromDate(Carbon::now()->format('Y'))->firstOfYear()->format('Y-m-d'));
        return $query->where('due_at', '>=', Carbon::createFromDate($currentDate)->addQuarter(2)->startOf('quarter')->format('Y-m-d'))
            ->where('due_at', '<=', Carbon::createFromDate($currentDate)->addQuarter(2)->endOf('quarter')->format('Y-m-d'));

    }

    public function scopeFourthQuarterOverdue($query)
    {
        $currentDate = (Carbon::createFromDate(Carbon::now()->format('Y'))->firstOfYear()->format('Y-m-d'));
        return $query->where('due_at', '>=', Carbon::createFromDate($currentDate)->addQuarter(3)->startOf('quarter')->format('Y-m-d'))
            ->where('due_at', '<=', Carbon::createFromDate($currentDate)->addQuarter(3)->endOf('quarter')->format('Y-m-d'));

    }

    public function scopeKeywords($query, $keywords = null)
    {
        if ($keywords)
        {
            $keywords = strtolower($keywords);

            $query->where(DB::raw('lower(number)'), 'like', '%' . $keywords . '%')
                ->orWhere('invoices.invoice_date', 'like', '%' . $keywords . '%')
                ->orWhere('due_at', 'like', '%' . $keywords . '%')
                ->orWhere('summary', 'like', '%' . $keywords . '%')
                ->orWhereIn('client_id', function ($query) use ($keywords) {
                    $query->select('id')->from('clients')->where(DB::raw("CONCAT_WS('^',LOWER(name))"), 'like', '%' . $keywords . '%');
                });
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
    | Other
    |--------------------------------------------------------------------------
    */

    public function customField($label, $rawHtml = true)
    {
        $customField = config('fi.customFields')->where('tbl_name', 'invoices')->where('field_label', $label)->first();

        if ($customField)
        {
            return CustomFieldsParser::getFieldValue($this->custom, $customField, $rawHtml);
        }

        return null;

    }

    public static function creditMemoListForClient($client_id)
    {
        return self::where([
            ['invoice_amounts.balance', '<', '0'],
            ['type', '=', 'credit_memo'],
            ['client_id', '=', $client_id],
        ])->join('invoice_amounts', 'invoice_amounts.invoice_id', '=', 'invoices.id')->get();
    }

    public static function invoiceListForClient($client_id)
    {
        return self::where([
            ['type', '=', 'invoice'],
            ['client_id', '=', $client_id],
        ])->Where(function ($q) {
            $q->orWhere(function ($e) {
                $e->whereNotNull('date_mailed')->whereStatus('sent');
            })->orWhereIn('status', ['draft', 'sent']);
        })->get();

    }

    public function getCreditApplication()
    {
        $result = PaymentInvoice::query()
            ->with(['payment', 'invoice'])
            ->whereHas('payment', function ($q) {
                $q->where('credit_memo_id', '=', $this->id);
            })
            ->get();

        return $result;
    }

    public function getShortSummaryAttribute()
    {
        return (mb_strlen($this->summary) > 50) ? mb_substr($this->summary, 0, 50) . '...' : $this->summary;
    }

    public function deleteTags(Invoice $invoice)
    {
        $invoice->tags()->delete();
    }

    public function checkCommission()
    {
        if (config('commission_enabled'))
        {
            $commissionCount = 0;
            $items           = InvoiceItem::with(['paidCommissions'])->whereInvoiceId($this->id)->get();;
            foreach ($items as $item)
            {
                $commissionCount += $item->paidCommissions->count();
            }
            if ($commissionCount > 0)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }

    }

    public function hasLineItemDiscount()
    {
        $hasDiscount = false;

        foreach ($this->items as $item)
        {
            if ($item->amount->discount_amount != 0)
            {
                $hasDiscount = true;
                break;
            }
        }

        return $hasDiscount;
    }

    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */

    public static function getHeaders()
    {
        return ['status', 'invoice', 'date', 'due', 'client', 'summary', 'tags', 'total', 'balance'];
    }

}