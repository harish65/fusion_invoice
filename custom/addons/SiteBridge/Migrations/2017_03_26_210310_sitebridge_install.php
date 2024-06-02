<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class SitebridgeInstall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('sb_clients'))
        {
            Schema::create('sb_clients', function (Blueprint $table)
            {
                $table->increments('id');
                $table->timestamps();
                $table->integer('fi_client_id');
                $table->integer('site_user_id');
                $table->string('name');
                $table->string('company');
                $table->string('email');

                $table->index('id');
                $table->index('fi_client_id');
                $table->index('site_user_id');
            });
        }

        if (!Schema::hasTable('sb_invoices'))
        {
            Schema::create('sb_invoices', function (Blueprint $table)
            {
                $table->increments('id');
                $table->timestamps();
                $table->integer('fi_invoice_id');
                $table->integer('site_invoice_id');
                $table->integer('site_user_id');
                $table->integer('fi_client_id');
                $table->decimal('total');

                $table->index('id');
                $table->index('fi_invoice_id');
                $table->index('site_invoice_id');
                $table->index('site_user_id');
                $table->index('fi_client_id');
            });
        }

        if (!Schema::hasTable('sb_invoice_items'))
        {
            Schema::create('sb_invoice_items', function (Blueprint $table)
            {
                $table->increments('id');
                $table->timestamps();
                $table->integer('sb_invoice_id');
                $table->integer('site_invoice_item_id');
                $table->string('license_key')->nullable();
                $table->string('item_name');
                $table->string('item_description');
                $table->decimal('price');

                $table->index('id');
                $table->index('sb_invoice_id');
                $table->index('site_invoice_item_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('sb_clients'))
        {
            Schema::drop('sb_clients');
        }
        if (Schema::hasTable('sb_invoices'))
        {
            Schema::drop('sb_invoices');
        }
        if (Schema::hasTable('sb_invoice_items'))
        {
            Schema::drop('sb_invoice_items');
        }
    }
}
