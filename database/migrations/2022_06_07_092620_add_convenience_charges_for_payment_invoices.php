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

class AddConvenienceChargesForPaymentInvoices extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('payment_invoices', 'convenience_charges'))
        {
            Schema::table('payment_invoices', function (Blueprint $table)
            {
                $table->decimal('convenience_charges', 10, 2)->default(0.00);
            });
        }
    }
}