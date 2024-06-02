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

use FI\Modules\Payments\Models\PaymentInvoice;
use FI\Support\CurrencyFormatter;
use FI\Support\NumberFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InvoiceAmount extends Model
{
    /**
     * Guarded properties
     * @var array
     */
    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function invoice()
    {
        return $this->belongsTo('FI\Modules\Invoices\Models\Invoice');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedSubtotalAttribute()
    {
        return CurrencyFormatter::format($this->attributes['subtotal'], $this->invoice->currency);
    }

    public function getFormattedTaxAttribute()
    {
        return CurrencyFormatter::format($this->attributes['tax'], $this->invoice->currency);
    }

    public function getFormattedTotalAttribute()
    {
        if ($this->invoice)
        {
            return CurrencyFormatter::format($this->attributes['total'] + $this->invoice->total_convenience_charges, $this->invoice->currency);
        }
    }

    public function getFormattedAbsoluteTotalAttribute()
    {
        return CurrencyFormatter::format(abs($this->attributes['total']), $this->invoice->currency);
    }

    public function getFormattedPaidAttribute()
    {
        return CurrencyFormatter::format($this->attributes['paid'] + $this->invoice->total_convenience_charges, $this->invoice->currency);
    }

    public function getFormattedBalanceAttribute()
    {
        return CurrencyFormatter::format($this->attributes['balance'], $this->invoice->currency);
    }

    public function getFormattedNumericBalanceAttribute()
    {
        return NumberFormatter::format($this->attributes['balance'], $this->invoice->currency);
    }

    public function getFormattedAbsoluteBalanceAttribute()
    {
        return sprintf("%.2f", abs($this->attributes['balance']));
    }

    public function getFormattedDiscountAttribute()
    {
        return CurrencyFormatter::format(-1 * abs($this->attributes['discount']), $this->invoice->currency);
    }

    /**
     * Retrieve the formatted total prior to conversion.
     * @return string
     */
    public function getFormattedTotalWithoutConversionAttribute()
    {
        return CurrencyFormatter::format($this->attributes['total'] / $this->invoice->exchange_rate);
    }
}