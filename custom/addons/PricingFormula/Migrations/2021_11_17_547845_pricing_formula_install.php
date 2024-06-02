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

class PricingFormulaInstall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('item_price_formulas'))
        {
            Schema::create('item_price_formulas', function (Blueprint $table)
            {
                $table->id();
                $table->timestamps();
                $table->string('name');
                $table->string('formula', 1024);
            });
        }

        if (!Schema::hasColumn('item_lookups', 'formula_id'))
        {
            Schema::table('item_lookups', function (Blueprint $table)
            {
                $table->integer('formula_id')->after('description')->nullable();
            });
        }
    }
}
