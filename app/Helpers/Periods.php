<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function periods()
{
    return [
        'all_time'          => trans('fi.all_time'),
        'today'             => trans('fi.today'),
        'yesterday'         => trans('fi.yesterday'),
        'last_7_days'       => trans('fi.last_7_days'),
        'last_30_days'      => trans('fi.last_30_days'),
        'this_month'        => trans('fi.this_month'),
        'last_month'        => trans('fi.last_month'),
        'year_to_date'      => trans('fi.this_year'),
        'last_year'         => trans('fi.last_year'),
        'this_quarter'      => trans('fi.this_quarter'),
        'last_quarter'      => trans('fi.last_quarter'),
        'first_quarter'     => trans('fi.first_quarter'),
        'second_quarter'    => trans('fi.second_quarter'),
        'third_quarter'     => trans('fi.third_quarter'),
        'fourth_quarter'    => trans('fi.fourth_quarter'),
        'custom_date_range' => trans('fi.custom_date_range'),
    ];
}