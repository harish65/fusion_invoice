<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Merchant\Support\Drivers;

use Carbon\Carbon;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Merchant\Models\MerchantPayment;
use FI\Modules\Merchant\Support\MerchantDriver;
use FI\Modules\PaymentMethods\Models\PaymentMethod;
use FI\Modules\Payments\Events\PaymentEmailed;
use FI\Modules\Payments\Models\Payment;
use FI\Modules\Payments\Models\PaymentInvoice;
use FI\Modules\Users\Models\User;
use Stripe\Checkout\Session as StripeSession;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class StripeDriver extends MerchantDriver
{
    public function getSettings()
    {
        return ['publishableKey', 'secretKey', 'apiVersion' => ['2021' => trans('fi.2021_prior'), '2022' => trans('fi.2022_later')]];
    }

    public function pay(Invoice $invoice)
    {
        $onlinePaymentChargesDetail = onlinePaymentChargesDetail($invoice);
        $amountValue                = $onlinePaymentChargesDetail['amountValue'];

        Stripe::setApiKey($this->getSetting('secretKey'));

        if ($this->getSetting('apiVersion') == 2022)
        {
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items'           => [
                    [
                        'price_data' =>
                            [
                                'currency'     => $invoice->currency_code,
                                'unit_amount'  => $amountValue * 100,
                                'product_data' => [
                                    'name'        => trans('invoice') . ' #' . $invoice->number,
                                    'description' => trans('invoice') . ' #' . $invoice->number,
                                ],
                            ],
                        'quantity'   => 1,
                    ],

                ],
                'mode'                 => 'payment',
                'success_url'          => route('merchant.pay.stripe.return', [$invoice->url_key]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'           => route('merchant.pay.stripe.cancel', [$invoice->url_key]),
            ]);

            return view('merchant.stripe')
                ->with('stripeSessionId', $session->id);
        }
        else
        {
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items'           => [
                    [
                        'name'        => trans('invoice') . ' #' . $invoice->number,
                        'description' => trans('invoice') . ' #' . $invoice->number,
                        'amount'      => $amountValue * 100,
                        'currency'    => $invoice->currency_code,
                        'quantity'    => 1,
                    ],
                ],
                'success_url'          => route('merchant.pay.stripe.return', [$invoice->url_key]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'           => route('merchant.pay.stripe.cancel', [$invoice->url_key]),
            ]);

            return view('merchant.stripe')
                ->with('stripeSessionId', $session->id);
        }

    }

    public function success(Invoice $invoice)
    {
        $stripeConvenienceCharges = (number_format($invoice->amount->balance, 2, '.', '') * (config('fi.feePercentage') / 100));

        Stripe::setApiKey($this->getSetting('secretKey'));

        $session = StripeSession::retrieve(request('session_id'));

        $paymentIntent = PaymentIntent::retrieve($session->payment_intent);

        if ($paymentIntent->status == 'succeeded')
        {
            onlineConvenienceCharges($invoice);

            $userId              = (auth()->user() != null) ? ((auth()->user()->user_type == 'paymentcenter_user') ? auth()->user()->id : User::whereUserType('system')->first()->id) : User::whereUserType('system')->first()->id;
            $onlinePaymentMethod = PaymentMethod::whereName(trans('fi.online_payment'))->first();

            $fiPayment = Payment::create([
                'user_id'           => $userId,
                'client_id'         => $invoice->client->id,
                'amount'            => $paymentIntent->amount / 100,
                'currency_code'     => $invoice->currency_code,
                'remaining_balance' => 0,
                'payment_method_id' => ($onlinePaymentMethod != null) ? $onlinePaymentMethod->id : config('fi.onlinePaymentMethod'),
                'paid_at'           => Carbon::now()->format('Y-m-d'),
            ]);

            if ($fiPayment)
            {
                $paymentInvoice                      = new PaymentInvoice();
                $paymentInvoice->payment_id          = $fiPayment->id;
                $paymentInvoice->invoice_id          = $invoice->id;
                $paymentInvoice->invoice_amount_paid = $invoice->amount->balance;
                $paymentInvoice->convenience_charges = $stripeConvenienceCharges;
                $paymentInvoice->save();

                if ($fiPayment->client->should_email_payment_receipt)
                {
                    event(new PaymentEmailed($fiPayment));
                }
            }

            MerchantPayment::saveByKey('Stripe', $fiPayment->id, 'id', $paymentIntent->id);

            return redirect()->route('clientCenter.public.invoice.show', [$invoice->url_key, $invoice->token])
                ->with('alertSuccess', trans('fi.payment_applied'));
        }

        return redirect()->route('clientCenter.public.invoice.show', [$invoice->url_key, $invoice->token])
            ->with('alert', trans('fi.payment_problem'));
    }

    public function cancel($urlKey, $token = null)
    {
        return redirect()->route('clientCenter.public.invoice.show', [$urlKey, $token])
            ->with('alert', trans('fi.payment_canceled'));
    }
}