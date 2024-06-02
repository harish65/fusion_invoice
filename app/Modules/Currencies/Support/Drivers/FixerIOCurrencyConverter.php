<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Currencies\Support\Drivers;

use Exception;
use Illuminate\Support\Facades\Log;

class FixerIOCurrencyConverter
{
    /**
     * Returns the currency conversion rate.
     *
     * @param string $from
     * @param string $to
     * @return string
     */
    public function convert($from, $to)
    {
        try
        {
            $fromTo = strtolower($from) . '/' . strtolower($to);
            $result = json_decode(file_get_contents('https://cdn.jsdelivr.net/gh/fawazahmed0/currency-api@1/latest/currencies/' . $fromTo . '.json'), true);
            $rate   = number_format($result[strtolower($to)], 7);
            return $rate;
        }
        catch (Exception $e)
        {
            Log::error("Currency Conversion API Error\r\n" . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
            return '1.0000000';
        }

    }
}