<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Support;

class Skins
{
    public static function lists()
    {
        return ['light-mode' => trans('fi.light-mode'), 'dark-mode' => trans('fi.dark-mode')];
    }

    public static function topBarColorLists()
    {
        return [
            'default'                            => trans('fi.light_grey'),
            'primary!!!white!!!danger!!!light'   => trans('fi.bg-primary'),
            'warning!!!dark-50!!!danger!!!dark'  => trans('fi.bg-warning'),
            'info!!!dark-50!!!danger!!!light'    => trans('fi.bg-info'),
            'danger!!!white-75!!!dark!!!light'   => trans('fi.bg-danger'),
            'success!!!white!!!danger!!!dark-50' => trans('fi.bg-success'),
            'indigo!!!white!!!danger!!!light'    => trans('fi.bg-indigo'),
            'lightblue!!!white!!!danger!!!light' => trans('fi.bg-lightblue'),
            'navy!!!white-50!!!danger!!!light'   => trans('fi.bg-navy'),
            'purple!!!white!!!danger!!!light'    => trans('fi.bg-purple'),
            'fuchsia!!!white!!!dark!!!light'     => trans('fi.bg-fuchsia'),
            'pink!!!dark-50!!!light!!!light'     => trans('fi.bg-pink'),
            'maroon!!!white!!!dark!!!light'      => trans('fi.bg-maroon'),
            'orange!!!dark-50!!!danger!!!dark'   => trans('fi.bg-orange'),
            'lime!!!dark-50!!!danger!!!dark'     => trans('fi.bg-lime'),
            'teal!!!dark-50!!!danger!!!dark'     => trans('fi.bg-teal'),
            'olive!!!white!!!danger!!!dark'      => trans('fi.bg-olive'),
        ];
    }

}