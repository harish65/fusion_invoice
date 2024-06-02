<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Mru\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Mru\Models\Mru;

class MruController extends Controller
{
    public function getMru()
    {
        $moduleIconMapping = [
            'clients'            => 'fa-users',
            'quotes'             => 'fa-file-alt',
            'invoices'           => 'fa-file-invoice',
            'recurring_invoices' => 'fa-sync',
            'payments'           => 'fa-credit-card',
            'expenses'           => 'fa-file-invoice-dollar',
            'reports'            => 'fas fa-chart-bar',
            'time_tracking'      => 'fa-clock',
            'containers'         => 'fa-truck',
            'commissions'        => 'fa-hand-holding-usd',
        ];

        if (config('commission_enabled'))
        {
            $mruList = Mru::whereUserId(auth()->user()->id)->limit(10)->orderBy('updated_at', 'DESC')->get();
        }
        else
        {
            $mruList = Mru::whereUserId(auth()->user()->id)->limit(10)->whereNotIn('module', ['commissions'])->orderBy('updated_at', 'DESC')->get();
        }

        return view('layouts._mru')->with('mruList', $mruList)->with('moduleIconMapping', $moduleIconMapping)->render();

    }
}