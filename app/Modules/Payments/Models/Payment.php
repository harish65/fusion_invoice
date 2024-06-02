<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Payments\Models;

use Carbon\Carbon;
use FI\Modules\Currencies\Models\Currency;
use FI\Modules\CustomFields\Support\CustomFieldsParser;
use FI\Support\CurrencyFormatter;
use FI\Support\DateFormatter;
use FI\Support\HTML;
use FI\Support\NumberFormatter;
use FI\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Payment extends Model
{
    use Sortable;

    /**
     * Guarded properties
     * @var array
     */
    protected $guarded = ['id'];

    protected $sortable = ['paid_at', 'invoices.invoice_date', 'invoices.number', 'invoices.summary', 'clients.name', 'amount', 'payment_methods.name', 'note'];

    protected $dates = ['paid_at'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function custom()
    {
        return $this->hasOne('FI\Modules\CustomFields\Models\PaymentCustom');
    }

    public function client()
    {
        return $this->belongsTo('FI\Modules\Clients\Models\Client');
    }

    public function creditMemo()
    {
        return $this->belongsTo('FI\Modules\Invoices\Models\Invoice', 'credit_memo_id', 'id');
    }

    public function paymentInvoice()
    {
        return $this->hasMany('FI\Modules\Payments\Models\PaymentInvoice');
    }

    public function mailQueue()
    {
        return $this->morphMany('FI\Modules\MailQueue\Models\MailQueue', 'mailable');
    }

    public function notes()
    {
        return $this->morphMany('FI\Modules\Notes\Models\Note', 'notable');
    }

    public function paymentMethod()
    {
        return $this->belongsTo('FI\Modules\PaymentMethods\Models\PaymentMethod');
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

    public function getCurrencyAttribute()
    {
        return Currency::getByCode($this->currency_code);
    }

    public function getFormattedPaidAtAttribute()
    {
        return DateFormatter::format($this->attributes['paid_at']);
    }

    public function getFormattedCreatedAtAttribute()
    {
        return DateFormatter::format($this->attributes['created_at']);
    }

    public function getFormattedNumericAmountAttribute()
    {
        return NumberFormatter::format($this->attributes['amount']);
    }

    public function getFormattedNumericRemainingBalanceAttribute()
    {
        return NumberFormatter::format($this->attributes['remaining_balance']);
    }

    public function getFormattedPaidAmountAttribute()
    {
        return NumberFormatter::format($this->attributes['amount'] - $this->attributes['remaining_balance']);
    }

    public function getFormattedAmountAttribute()
    {
        return CurrencyFormatter::format($this->attributes['amount'], $this->client->currency);
    }

    public function getFormattedAmountWithCurrencyAttribute()
    {
        if (count($this->paymentInvoice) > 0)
        {
            return CurrencyFormatter::format($this->attributes['amount'], $this->currency);
        }
        else
        {
            return CurrencyFormatter::format($this->attributes['amount'], $this->currency);
        }
    }

    public function getFormattedRemainingBalanceAttribute()
    {
        return CurrencyFormatter::format($this->attributes['remaining_balance'], $this->currency);
    }

    public function getFormattedRemainingBalanceWithCurrencyAttribute()
    {
        if (count($this->paymentInvoice) > 0)
        {
            return CurrencyFormatter::format($this->attributes['remaining_balance'], $this->currency);
        }
        else
        {
            return CurrencyFormatter::format($this->attributes['remaining_balance'], $this->currency);
        }
    }

    public function getFormattedNoteAttribute()
    {
        return nl2br($this->attributes['note']);
    }

    public function getPaymentReceiptHtmlAttribute()
    {
        return HTML::payment($this);
    }

    public function getFormattedNotesAttribute()
    {
        if ($this->note && strlen($this->note) > 40)
        {
            return '<span data-toggle="tooltip" title="' . $this->note . '">' . mb_substr($this->note, 0, 40).'...' . '</span>';
        }
        elseif ($this->note && strlen($this->note) < 40)
        {
            return $this->note;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeThisYear($query)
    {
        return $query->where(DB::raw('YEAR(paid_at)'), '=', DB::raw('YEAR(CURRENT_DATE())'));
    }

    public function scopeThisMonth($query)
    {
        return $query->where(DB::raw('MONTH(paid_at)'), '=', DB::raw('MONTH(CURRENT_DATE())'))
                     ->where(DB::raw('YEAR(paid_at)'), '=', DB::raw('YEAR(CURRENT_DATE())'));
    }

    public function scopeThisQuarter($query)
    {
        return $query->where('paid_at', '>=', Carbon::now()->firstOfQuarter())
                     ->where('paid_at', '<=', Carbon::now()->lastOfQuarter());
    }

    public function scopeLastMonth($query)
    {
        return $query->where(DB::raw('paid_at'), '>=', Carbon::now()->subMonths(1)->firstOfMonth())
                     ->where(DB::raw('paid_at'), '<=', Carbon::now()->subMonths(1)->lastOfMonth());
    }

    public function scopeLastQuarter($query)
    {
        return $query->where('paid_at', '>=', Carbon::now()->subQuarters(1)->firstOfQuarter())
                     ->where('paid_at', '<=', Carbon::now()->subQuarters(1)->lastOfQuarter());
    }

    public function scopeLastYear($query)
    {
        return $query->where('paid_at', '>=', Carbon::now()->subYears(1)->firstOfYear())
                     ->where('paid_at', '<=', Carbon::now()->subYears(1)->lastOfYear());
    }

    public function scopeDateRange($query, $fromDate, $toDate)
    {
        return $query->where('paid_at', '>=', $fromDate)
                     ->where('paid_at', '<=', $toDate);
    }

    public function scopeToday($query)
    {
        return $query->where('paid_at', '=', Carbon::now()->format('Y-m-d'));
    }

    public function scopeYesterday($query)
    {
        return $query->where('paid_at', '=', Carbon::yesterday()->format('Y-m-d'));
    }

    public function scopeLast7Days($query)
    {
        return $query->where('paid_at', '>=', Carbon::now()->subDays(6)->format('Y-m-d'))
                     ->where('paid_at', '<=', Carbon::now()->format('Y-m-d'));
    }

    public function scopeLast30Days($query)
    {
        return $query->where('paid_at', '>=', Carbon::now()->subDays(29)->format('Y-m-d'))
                     ->where('paid_at', '<=', Carbon::now()->format('Y-m-d'));
    }

    public function scopeFirstQuarter($query)
    {
        $currentDate = (Carbon::createFromDate(Carbon::now()->format('Y'))->firstOfYear()->format('Y-m-d'));
        return $query->where('paid_at', '>=', Carbon::createFromDate($currentDate)->addQuarter(0)->startOf('quarter')->format('Y-m-d'))
                     ->where('paid_at', '<=', Carbon::createFromDate($currentDate)->addQuarter(0)->endOf('quarter')->format('Y-m-d'));
    }

    public function scopeSecondQuarter($query)
    {
        $currentDate = (Carbon::createFromDate(Carbon::now()->format('Y'))->firstOfYear()->format('Y-m-d'));
        return $query->where('paid_at', '>=', Carbon::createFromDate($currentDate)->addQuarter(1)->startOf('quarter')->format('Y-m-d'))
                     ->where('paid_at', '<=', Carbon::createFromDate($currentDate)->addQuarter(1)->endOf('quarter')->format('Y-m-d'));
    }

    public function scopeThirdQuarter($query)
    {
        $currentDate = (Carbon::createFromDate(Carbon::now()->format('Y'))->firstOfYear()->format('Y-m-d'));
        return $query->where('paid_at', '>=', Carbon::createFromDate($currentDate)->addQuarter(2)->startOf('quarter')->format('Y-m-d'))
                     ->where('paid_at', '<=', Carbon::createFromDate($currentDate)->addQuarter(2)->endOf('quarter')->format('Y-m-d'));
    }

    public function scopeFourthQuarter($query)
    {
        $currentDate = (Carbon::createFromDate(Carbon::now()->format('Y'))->firstOfYear()->format('Y-m-d'));
        return $query->where('paid_at', '>=', Carbon::createFromDate($currentDate)->addQuarter(3)->startOf('quarter')->format('Y-m-d'))
                     ->where('paid_at', '<=', Carbon::createFromDate($currentDate)->addQuarter(3)->endOf('quarter')->format('Y-m-d'));
    }

    public function scopeKeywords($query, $keywords)
    {
        $keywords = strtolower($keywords);

        if ($keywords)
        {

            $dateFormats     = DateFormatter::formats();
            $mysqlDateFormat = $dateFormats[config('fi.dateFormat')]['mysql'];
            $keywords        = strtolower($keywords);

            $query->where('payments.created_at', 'like', '%' . $keywords . '%')
                  ->orWhere('payments.note', 'like', '%' . $keywords . '%')
                  ->orWhere(DB::raw("DATE_FORMAT(" . DB::getTablePrefix() . "payments.paid_at,'" . $mysqlDateFormat . "')"), 'like', '%' . $keywords . '%')
                  ->orWhereIn('payments.id', function ($query) use ($keywords)
                  {
                      $query->select('payment_id')->from('payment_invoices')->leftJoin('invoices', 'payment_invoices.invoice_id', '=', 'invoices.id')
                            ->where(DB::raw('lower(' . DB::getTablePrefix() . 'invoices.number)'), 'like', '%' . $keywords . '%')
                            ->orWhere('invoices.summary', 'like', '%' . $keywords . '%');
                  })
                  ->orWhereIn('payments.client_id', function ($query) use ($keywords)
                  {
                      $query->select('id')->from('clients')->where(DB::raw("CONCAT_WS('^',LOWER(name))"), 'like', '%' . $keywords . '%');
                  })
                  ->orWhereIn('payment_method_id', function ($query) use ($keywords)
                  {
                      $query->select('id')->from('payment_methods')->where(DB::raw('lower(name)'), 'like', '%' . $keywords . '%');
                  });
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
                        $query->orWhere('payments.' . $key, 'like', '%' . $value . '%');
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

    public function scopeInvoiceId($query, $invoiceId)
    {
        if ($invoiceId)
        {
            $query->whereHas('invoice', function ($query) use ($invoiceId)
            {
                $query->where('id', $invoiceId);
            });
        }

        return $query;
    }

    public function scopeClientId($query, $clientId)
    {
        if ($clientId)
        {
            $query->whereHas('client', function ($query) use ($clientId)
            {
                $query->where('id', $clientId);
            });
        }

        return $query;
    }

    public function scopeInvoiceNumber($query, $invoiceNumber)
    {
        if ($invoiceNumber)
        {
            $query->whereHas('invoice', function ($query) use ($invoiceNumber)
            {
                $query->where('number', $invoiceNumber);
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

    public function scopePrePayment($query)
    {
        return $query->where('type', 'pre-payment');
    }

    /*
    |--------------------------------------------------------------------------
    | Other
    |--------------------------------------------------------------------------
    */

    public function customField($label, $rawHtml = true)
    {
        $customField = config('fi.customFields')->where('tbl_name', 'payments')->where('field_label', $label)->first();

        if ($customField)
        {
            return CustomFieldsParser::getFieldValue($this->custom, $customField, $rawHtml);
        }

        return null;

    }

    public static function prePaymentListForClient($client_id)
    {
        return self::where([
            ['client_id', '=', $client_id],
            ['remaining_balance', '>', 0],
        ])->get();
    }
}
