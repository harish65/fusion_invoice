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

class AlterQuoteItemAmountsForDiscount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('quote_item_amounts','discount_amount'))
        {
            Schema::table('quote_item_amounts', function (Blueprint $table)
            {
                $table->decimal('discount_amount', 10, 2)->nullable()->default(0.00)->after('tax');
            });
        }
    }

}
