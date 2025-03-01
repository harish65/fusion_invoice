<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Merchant\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantPayment extends Model
{
    protected $table = 'merchant_payments';

    protected $guarded = ['id'];

    public static function getByKey($driver, $paymentId, $key)
    {
        $setting = self::where('driver', $driver)
            ->where('payment_id', $paymentId)
            ->where('merchant_key', $key)
            ->first();

        if ($setting)
        {
            return $setting->merchant_value;
        }

        return null;
    }

    public static function saveByKey($driver, $paymentId, $key, $value)
    {
        $setting = self::firstOrNew([
            'driver'       => $driver,
            'payment_id'   => $paymentId,
            'merchant_key' => $key,
        ]);

        $setting->merchant_value = $value;

        $setting->save();
    }
}