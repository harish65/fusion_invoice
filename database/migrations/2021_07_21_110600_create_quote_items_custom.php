<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateQuoteItemsCustom extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('quote_items_custom'))
        {
            Schema::create('quote_items_custom', function (Blueprint $table)
            {
                $table->id('quote_item_id');
                $table->timestamps();
            });
        }
        if (Schema::hasColumn('quote_items_custom', 'quote_item_id'))
        {
            $quoteItems = DB::table('quote_items')->whereNotIn('id', function ($query)
            {
                $query->select('quote_item_id')->from('quote_items_custom');
            })->get();

            foreach ($quoteItems as $quoteItem)
            {
                DB::table('quote_items_custom')->insert([
                    'quote_item_id' => $quoteItem->id,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ]);
            }
        }
    }
}