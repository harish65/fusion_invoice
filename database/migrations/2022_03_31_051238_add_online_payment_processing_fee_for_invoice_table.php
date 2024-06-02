<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOnlinePaymentProcessingFeeForInvoiceTable extends Migration
{
    public function up()
    {

        Schema::table('invoices', function (Blueprint $table)
        {
            if (!(Schema::hasColumn('invoices', 'online_payment_processing_fee')))
            {
                $table->enum('online_payment_processing_fee', ['yes', 'no'])->default('no');
            }
            if (!(Schema::hasColumn('invoices', 'convenience_charges')))
            {
                $table->decimal('convenience_charges', 10, 2)->default(0.00);
            }
        });
    }

}
