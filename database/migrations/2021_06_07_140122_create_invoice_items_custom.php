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

class CreateInvoiceItemsCustom extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('invoice_items_custom'))
        {
            Schema::create('invoice_items_custom', function (Blueprint $table)
            {
                $table->integer('invoice_item_id');
                $table->timestamps();
                $table->primary('invoice_item_id');
            });
        }

        if (Schema::hasColumn('invoice_items_custom', 'invoice_item_id'))
        {
            //Insert missing invoice_items_custom records
            $invoiceItems = DB::table('invoice_items')->whereNotIn('id', function ($query)
            {
                $query->select('invoice_item_id')->from('invoice_items_custom');
            })->get();

            $insertData = [];

            foreach ($invoiceItems as $invoiceItem)
            {
                $insertData[] = [
                    'invoice_item_id' => $invoiceItem->id,
                    'created_at'      => Carbon::now(),
                    'updated_at'      => Carbon::now(),
                ];
            }

            $insertData = collect($insertData);

            $chunks = $insertData->chunk(1000);

            foreach ($chunks as $chunk)
            {
                DB::table('invoice_items_custom')->insert($chunk->toArray());
            }
        }
    }
}
