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

class AlterQuoteItemsForDiscount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumns('quote_items',['original_price','discount_type','discount']))
        {
            Schema::table('quote_items', function (Blueprint $table)
            {
                $table->decimal('original_price', 10, 2)->nullable()->default(0.00)->after('display_order');
                $table->string('discount_type')->nullable()->after('original_price');
                $table->decimal('discount', 10, 2)->nullable()->default(0.00)->after('discount_type');
            });
        }
    }

}
