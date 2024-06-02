<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function getCurrencyClass($currency)
{
    if ($currency == 'AUD')
    {
        return 'fa-dollar-sign';
    }
    elseif ($currency == 'CAD')
    {
        return 'fa-dollar-sign';
    }
    elseif ($currency == 'EUR')
    {
        return 'fa-euro-sign';
    }
    elseif ($currency == 'GBP')
    {
        return 'fa-pound-sign';
    }
    elseif ($currency == 'USD')
    {
        return 'fa-dollar-sign';
    }

}

function getCurrencySign($currency)
{
    if (in_array($currency, ['AUD', 'CAD', 'USD']))
    {
        return '$';
    }
    elseif ($currency == 'EUR')
    {
        return '€';
    }
    elseif ($currency == 'GBP')
    {
        return '£';
    }

}
