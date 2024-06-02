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

class CreateItemLookupsCustom extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('item_lookups_custom'))
        {
            Schema::create('item_lookups_custom', function (Blueprint $table)
            {
                $table->integer('item_lookup_id');
                $table->timestamps();
                $table->primary('item_lookup_id');
            });
        }
        if (Schema::hasColumn('item_lookups_custom', 'item_lookup_id'))
        {
            //Insert missing item_lookups_custom records
            $itemLookups = DB::table('item_lookups')->whereNotIn('id', function ($query)
            {
                $query->select('item_lookup_id')->from('item_lookups_custom');
            })->get();


            foreach ($itemLookups as $itemLookup)
            {
                DB::table('item_lookups_custom')->insert([
                    'item_lookup_id' => $itemLookup->id,
                    'created_at'     => Carbon::now(),
                    'updated_at'     => Carbon::now(),
                ]);
            }
        }
    }
}
