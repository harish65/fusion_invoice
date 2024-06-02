<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Invoices\Support;

use FI\Modules\Invoices\Models\InvoiceAmount;

class CreditReturned
{
    public function adjust($creditMemo, $paymentAmount = null)
    {

        $creditMemoAmount = InvoiceAmount::where('invoice_id', $creditMemo->id)->first();

        if ($creditMemoAmount)
        {
            if ($paymentAmount != null)
            {
                try
                {
                    $totalApplied              = $paymentAmount;
                    $creditMemoAmount->paid    = (-1 * (abs($creditMemoAmount->paid) - $totalApplied));
                    $creditMemoAmount->balance = (-1 * (abs($creditMemoAmount->balance) + $totalApplied));
                    $creditMemoAmount->save();
                }
                catch (\Exception $e)
                {
                    ?>
                    <script>
                        alertify.error(<?php echo $e->getMessage() ?>);
                    </script>
                    <?php
                }
            }

            if($creditMemoAmount->total < $creditMemoAmount->balance and $creditMemo->status_text != 'canceled')
            {
                $creditMemo->status = 'applied';
                $creditMemo->save();
            }
            else
            {
                $creditMemo->status = 'draft';
                $creditMemo->save();
            }
        }
    }
}