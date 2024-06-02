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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddInvoiceStatus extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('invoices', 'status'))
        {
            Schema::table('invoices', function (Blueprint $table)
            {
                DB::statement("ALTER TABLE " . DB::getTablePrefix() . "invoices MODIFY status ENUM('draft', 'sent', 'applied', 'canceled')");
            });
        }
    }
}
