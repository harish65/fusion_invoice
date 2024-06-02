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

class AddSupportForThirdPartyBillPayClientTypeForClients extends Migration
{

    public function up()
    {
        if (!Schema::hasColumns('clients', ['invoices_paid_by','email_default','allow_child_accounts','third_party_bill_payer']))
        {
            Schema::table('clients', function (Blueprint $table)
            {
                $table->integer('invoices_paid_by')->nullable();
                $table->string('email_default')->nullable();
                $table->boolean('allow_child_accounts')->default(0)->nullable();
                $table->boolean('third_party_bill_payer')->default(0)->nullable();

            });
        }

    }

}