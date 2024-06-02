<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @param $sizeStr
 * @return int
 */
function returnBytes($sizeStr)
{
    switch (substr($sizeStr, -1))
    {
        case 'M':
        case 'm':
            return (int)$sizeStr * 1048576;
        case 'K':
        case 'k':
            return (int)$sizeStr * 1024;
        case 'G':
        case 'g':
            return (int)$sizeStr * 1073741824;
        default:
            return (int)$sizeStr;
    }
}