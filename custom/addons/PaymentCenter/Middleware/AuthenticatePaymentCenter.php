<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\PaymentCenter\Middleware;

use Closure;

class AuthenticatePaymentCenter
{
    public function handle($request, Closure $next)
    {
        if (auth()->guest() or (auth()->check() and !session()->has('paymentcenter')))
        {
            if ($request->ajax())
            {
                return response('Unauthorized.', 401);
            }
            else
            {
                return redirect()->route('paymentCenter.login');
            }
        }

        return $next($request);
    }
}
