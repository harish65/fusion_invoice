<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use FI\Modules\Payments\Models\Payment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddMissingCurrencyCodeOnPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try
        {
            $payments = Payment::whereCurrencyCode(null)->get();

            DB::beginTransaction();

            if ($payments->count() > 0)
            {

                foreach ($payments as $payment)
                {
                    if ($payment->type == 'pre-payment')
                    {
                        $payment->currency_code = $payment->client->currency_code;
                        $payment->save();
                    }
                    elseif ($payment->type == 'single')
                    {
                        $payment->currency_code = isset($payment->paymentInvoice[0]->currency_code) ? $payment->paymentInvoice[0]->currency_code : $payment->client->currency_code;
                        $payment->save();
                    }
                    elseif ($payment->type == 'credit-memo')
                    {
                        $payment->currency_code = $payment->creditMemo->currency_code;
                        $payment->save();
                    }
                }
            }

            DB::commit();
        }
        catch (\PDOException $e)
        {
            DB::rollBack();
        }
    }

}