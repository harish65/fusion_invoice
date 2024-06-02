<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\PaymentCenter\Controllers;

use Addons\PaymentCenter\Validators\PaymentCenterSessionValidator;
use FI\Http\Controllers\Controller;

class PaymentCenterSessionController extends Controller
{
    public function __construct(PaymentCenterSessionValidator $paymentCenterSessionValidator)
    {
        $this->paymentCenterSessionValidator = $paymentCenterSessionValidator;
    }

    public function login()
    {
        return view('paymentcenter.login');
    }

    public function attempt()
    {
        $validator  = $this->paymentCenterSessionValidator->getValidator(request()->all());
        $rememberMe = (request('remember_me')) ? true : false;

        if ($validator->fails())
        {
            return redirect()->route('paymentCenter.login')->withErrors($validator);
        }

        if (!auth()->attempt(['email' => request('email'), 'password' => request('password'), 'status' => 1, 'user_type' => function ($query) {
            $query->whereUserType('paymentcenter_user');
        }], $rememberMe))
        {
            return redirect()->route('paymentCenter.login')->with('error', trans('fi.invalid_credentials'));
        }

        session(['paymentcenter' => true, 'thirdpartyauth' => true]);

        return redirect()->route('paymentCenter.dashboard');

    }

    public function logout()
    {
        auth()->logout();

        session()->flush();

        return redirect()->route('paymentCenter.login');
    }
}