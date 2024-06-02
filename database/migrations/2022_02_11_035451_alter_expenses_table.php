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

class AlterExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        if(Schema::hasColumns('expenses',['client_id','vendor_id','invoice_id']))
        {
            Schema::table('expenses', function (Blueprint $table)
            {
                $table->integer('client_id')->nullable()->change();
                $table->integer('vendor_id')->nullable()->change();
                $table->integer('invoice_id')->nullable()->change();
            });
        }
    }

}
