<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Currencies\Models;

use Illuminate\Support\Facades\Cache;

class CurrencyObserver
{
    public function creating(Currency $currency)
    {
        Cache::forget('currency');
    }

    public function updating(Currency $currency)
    {
        Cache::forget('currency');
    }

    public function saving(Currency $currency)
    {
        Cache::forget('currency');
    }

    public function deleting(Currency $currency)
    {
        Cache::forget('currency');
    }
}