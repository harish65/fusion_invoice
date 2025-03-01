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

class Expenses extends Migration
{
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->date('expense_date');
            $table->integer('user_id');
            $table->integer('category_id');
            $table->integer('client_id');
            $table->integer('vendor_id');
            $table->integer('invoice_id');
            $table->string('description')->nullable();
            $table->string('amount');

            $table->index('category_id');
            $table->index('client_id');
            $table->index('vendor_id');
            $table->index('invoice_id');
        });

        Schema::create('expense_vendors', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
        });

        Schema::create('expense_categories', function (Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
        });
    }
}
