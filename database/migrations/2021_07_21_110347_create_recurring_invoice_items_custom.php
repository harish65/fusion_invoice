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

class CreateRecurringInvoiceItemsCustom extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('recurring_invoice_items_custom'))
        {
            Schema::create('recurring_invoice_items_custom', function (Blueprint $table)
            {
                $table->integer('recurring_invoice_item_id');
                $table->timestamps();
                $table->primary('recurring_invoice_item_id');
            });
        }

        if (Schema::hasColumn('recurring_invoice_items_custom','recurring_invoice_item_id'))
        {
            $recurringInvoiceItems = DB::table('recurring_invoice_items')->whereNotIn('id', function ($query)
            {
                $query->select('recurring_invoice_item_id')->from('recurring_invoice_items_custom');
            })->get();

            foreach ($recurringInvoiceItems as $recurringInvoiceItem)
            {
                DB::table('recurring_invoice_items_custom')->insert([
                    'recurring_invoice_item_id' => $recurringInvoiceItem->id,
                    'created_at'                => Carbon::now(),
                    'updated_at'                => Carbon::now(),
                ]);
            }
        }
    }
}
