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
use FI\Modules\Payments\Models\Payment as FIPayment;
use FI\Modules\Payments\Models\PaymentInvoice;
use FI\Modules\Users\Models\User;
use Illuminate\Support\Facades\Log;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class PayPalDriver extends MerchantDriver
{
    public function getSettings()
    {
        return ['clientId', 'clientSecret', 'mode' => ['sandbox' => trans('fi.sandbox'), 'live' => trans('fi.live')]];
    }

    public function pay(Invoice $invoice)
    {
        $onlinePaymentChargesDetail = onlinePaymentChargesDetail($invoice);
        $amountValue                = $onlinePaymentChargesDetail['amountValue'];
        $addChargesItem             = $onlinePaymentChargesDetail['addChargesItem'];
        $feeCharges                 = $onlinePaymentChargesDetail['feeCharges'];

        $apiContext = $this->getApiContext();

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $item = new Item();
        $item->setName(trans('fi.invoice') . ' #' . $invoice->number)
            ->setCurrency($invoice->currency_code)
            ->setQuantity(1)
            ->setPrice($invoice->amount->balance + 0);

        $item2 = new Item();
        $item2->setName(trans('fi.convenience_charges'))
            ->setCurrency($invoice->currency_code)
            ->setQuantity(1)
            ->setPrice((($addChargesItem == true) ? $feeCharges : 0) + 0);

        $itemList = new ItemList();
        $itemList->setItems([$item, $item2]);

        $amount = new Amount();
        $amount->setCurrency($invoice->currency_code)
            ->setTotal($amountValue + 0);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setDescription(trans('fi.invoice') . ' #' . $invoice->number)
            ->setInvoiceNumber(uniqid())
            ->setItemList($itemList);

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(route('merchant.pay.paypal.return', [$invoice->url_key]))
            ->setCancelUrl(route('merchant.pay.paypal.cancel', [$invoice->url_key]));

        $payment = new \PayPal\Api\Payment();

        $payment->setIntent('sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions([$transaction]);

        try
        {
            $payment->create($apiContext);

            return redirect($payment->getApprovalLink());
        }
        catch (\Throwable $e)
        {
            Log::error($e->getMessage());

            return redirect()->route('clientCenter.public.invoice.show', [$invoice->url_key, $invoice->token])
                ->with('error', trans('fi.payment_problem'));
        }
    }

    public function success(Invoice $invoice)
    {
        $paymentConvenienceCharges = (number_format($invoice->amount->balance, 2, '.', '') * (config('fi.feePercentage') / 100));

        $paymentId  = request('paymentId');
        $apiContext = $this->getApiContext();
        $payment    = Payment::get($paymentId, $apiContext);

        $execution = new PaymentExecution();
        $execution->setPayerId(request('PayerID'));

        $transaction = new Transaction();
        $amount      = new Amount();

        if ($invoice->online_payment_processing_fee == 'yes' && config('fi.feePercentage') != '')
        {
            $amountValue = round((number_format($invoice->amount->balance, 2, '.', '')) + $paymentConvenienceCharges, 2);
        }
        else
        {
            $amountValue = $invoice->amount->balance + 0;
        }

        $amount->setCurrency($invoice->currency_code)->setTotal($amountValue);

        $transaction->setAmount($amount);

        $execution->addTransaction($transaction);

        $payment->execute($execution, $apiContext);

        $payment = Payment::get($paymentId, $apiContext);

        if ($payment->getState() == 'approved')
        {
            onlineConvenienceCharges($invoice);

            $userId              = (auth()->user() != null) ? ((auth()->user()->user_type == 'paymentcenter_user') ? auth()->user()->id : User::whereUserType('system')->first()->id) : User::whereUserType('system')->first()->id;
            $onlinePaymentMethod = PaymentMethod::whereName(trans('fi.online_payment'))->first();

            foreach ($payment->getTransactions() as $transaction)
            {
                $fiPayment = FIPayment::create([
                    'user_id'           => $userId,
                    'client_id'         => $invoice->client->id,
                    'amount'            => $transaction->getAmount()->getTotal(),
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
                    $paymentInvoice->convenience_charges = $paymentConvenienceCharges;
                    $paymentInvoice->save();

                    if ($fiPayment->client->should_email_payment_receipt)
                    {
                        event(new PaymentEmailed($fiPayment));
                    }
                }
                MerchantPayment::saveByKey('PayPal', $fiPayment->id, 'id', $payment->getId());
            }

            return redirect()->route('clientCenter.public.invoice.show', [$invoice->url_key, $invoice->token])
                ->with('alertSuccess', trans('fi.payment_applied'));
        }

        return redirect()->route('clientCenter.public.invoice.show', [$invoice->url_key, $invoice->token])
            ->with('alertError', trans('fi.payment_problem'));
    }

    private function getApiContext()
    {
        $credential = new OAuthTokenCredential($this->getSetting('clientId'), $this->getSetting('clientSecret'));

        $apiContext = new ApiContext($credential);

        $apiContext->setConfig(['mode' => $this->getSetting('mode')]);

        return $apiContext;
    }

    public function cancel($urlKey, $token = null)
    {
        return redirect()->route('clientCenter.public.invoice.show', [$urlKey, $token])
            ->with('alert', trans('fi.payment_canceled'));
    }
}