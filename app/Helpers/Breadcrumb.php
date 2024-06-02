<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function concat($array, $keys, $glue = " ")
{
    $values = array_intersect_key($array, array_flip($keys));
    return implode($glue, $values);
}


function breadcrumbs($separator = '&nbsp; ', $home = ' Home')
{
    $path         = array_filter(explode('/', request()->getPathInfo()));
    $getHost      = (strpos(request()->url(), 'localhost') == true) ? request()->getHost() . request()->getBaseUrl() : request()->getHost();

    $base         = request()->secure() ? 'https' : 'http';
    $base         = $base . '://' . $getHost . '/';
    
    $breadcrumbs  = [" <li class='breadcrumb-item'><a href=\"$base\">$home</a></li>"];
    $first        = pos(array_keys($path));
    $last         = last(array_keys($path));
    $numericValue = '';
    $booleanValue = true;
    $depth        = [];

    foreach ($path as $key => $value)
    {
        if (strpos($value, '_') != false || $numericValue == null)
        {
            $booleanValue = false;
        }

        if (is_numeric($value) && $value != 'create')
        {
            $numericValue = $key;
            break;
        }
        else
        {
            if (in_array($value, ['create']) != true)
            {
                $depth[] = $key;
            }

        }

    }

    if (count($path) >= 4 && isset($path[$numericValue]) && is_numeric($path[$numericValue]))
    {
        $newPath = str_replace(' ', '/', concat($path, $depth));

        foreach ($depth as $unsetKey)
        {
            unset($path[$unsetKey]);
        }
        array_unshift($path, $newPath);
        $path  = array_values($path);
        $first = pos(array_keys($path));
        $last  = last(array_keys($path));
    }
    else
    {
        if ($path[1] != 'import')
        {
            if ($booleanValue == true)
            {

                $newPath = str_replace(' ', '/', concat($path, $depth));
                foreach ($depth as $unsetKey)
                {
                    unset($path[$unsetKey]);
                }
                array_unshift($path, $newPath);
                $path  = array_values($path);
                $first = pos(array_keys($path));
                $last  = last(array_keys($path));

            }
        }
    }
    foreach ($path as $x => $crumb)
    {
        $title = languageTranslator($crumb);

        if ($crumb == 'time_tracking' || $crumb == 'commission' || $crumb == 'categories' || $crumb == 'item' || $crumb == 'reports' || $crumb == 'site_bridge')
        {
            $breadcrumbs[] = '<li class="breadcrumb-item active">' . $title . '</li>';
        }

        elseif ($x == $first && $crumb != 'time_tracking' && count($path) != 1)
        {

            if ($crumb == 'report')
            {
                $breadcrumbs[] = '<li class="breadcrumb-item active">' . $title . '</li>';
            }
            else
                $breadcrumbs[] = "<li class='breadcrumb-item'><a href=\"$base$crumb\">$title</a></li>";
        }
        elseif ($x == $last)
        {
            $breadcrumbs[] = '<li class="breadcrumb-item active">' . $title . '</li>';
        }

    }
    return implode($separator, $breadcrumbs);
}

function languageTranslator($word)
{
    $crumb = str_replace('/', '_', $word);
    switch ($crumb)
    {
        case in_array($crumb, ['commission_type', 'report_commission', 'commission', 'recurring_invoice_commission', 'recurring_commissions', 'invoice_commission']):
            return trans('Commission::lang.' . $crumb);
            break;
        case in_array($crumb, ['time_tracking_projects', 'time_tracking', 'projects', 'timesheet']):
            return trans('TimeTracking::lang.' . $crumb);
            break;
        case in_array($crumb, ['item_price_formula_create', 'item_price_formula','formula']):
            return trans('PricingFormula::lang.' . $crumb);
            break;
        case in_array($crumb, ['payctr']):
            return trans('PaymentCenter::lang.' . $crumb);
            break;
        case in_array($crumb, ['site_bridge']):
            return trans('SiteBridge::lang.' . $crumb);
            break;
        default:
            return trans('fi.' . $crumb);
            break;
    }
}