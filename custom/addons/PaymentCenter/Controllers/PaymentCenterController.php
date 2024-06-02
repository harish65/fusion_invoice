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

use Addons\PaymentCenter\Repositories\PaymentCenterInvoiceRepository;
use Addons\PaymentCenter\Validators\PaymentCenterSearchValidator;
use FI\Http\Controllers\Controller;

class PaymentCenterController extends Controller
{
    public function __construct(
        PaymentCenterInvoiceRepository $paymentCenterInvoiceRepository,
        PaymentCenterSearchValidator $paymentCenterSearchValidator)
    {
        $this->paymentCenterInvoiceRepository = $paymentCenterInvoiceRepository;
        $this->paymentCenterSearchValidator   = $paymentCenterSearchValidator;
    }

    public function dashboard()
    {
        return view('paymentcenter.dashboard')->with('invoices', $this->paymentCenterInvoiceRepository->search(request()->only(['name', 'phone', 'invoice_number'])));
    }

    public function search()
    {
        $validator = $this->paymentCenterSearchValidator->getValidator(request()->all());

        if ($validator->fails())
        {
            return redirect()->route('paymentCenter.dashboard')
                             ->withErrors($validator)
                             ->withInput();
        }

        return view('paymentcenter.dashboard')
            ->with('invoices', $this->paymentCenterInvoiceRepository->search(request()->only(['name', 'phone', 'invoice_number'])));
    }
}