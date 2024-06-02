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

class CommissionInstall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('recurring_invoice_item_commissions'))
        {
            Schema::create('recurring_invoice_item_commissions', function (Blueprint $table)
            {
                $table->increments('id');
                $table->timestamps();
                $table->integer('recurring_invoice_item_id');
                $table->integer('user_id');
                $table->integer('type_id');
                $table->string('note')->nullable();
                $table->decimal('amount', 10, 2)->nullable();
                $table->date('stop_date');
            });
        }
        if (!Schema::hasTable('invoice_item_commissions'))
        {
            Schema::create('invoice_item_commissions', function (Blueprint $table)
            {
                $table->increments('id');
                $table->timestamps();
                $table->integer('invoice_item_id');
                $table->integer('user_id');
                $table->integer('type_id');
                $table->string('note')->nullable();
                $table->decimal('amount', 10, 2)->nullable();
                $table->enum('status', ['cancelled', 'new', 'approved', 'paid']);
            });
        }
        if (!Schema::hasTable('commission_types'))
        {
            Schema::create('commission_types', function (Blueprint $table)
            {
                $table->increments('id');
                $table->timestamps();
                $table->string('name');
                $table->enum('method', ['formula', 'manual_entry']);
                $table->string('formula');
            });
        }
    }
}
