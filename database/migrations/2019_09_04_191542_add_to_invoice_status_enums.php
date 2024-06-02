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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddToInvoiceStatusEnums extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('invoices', 'status'))
        {
            DB::statement("ALTER TABLE " . DB::getTablePrefix() . "invoices MODIFY status ENUM('draft', 'sent', 'paid', 'canceled', 'unpaid', 'mailed')");
        }
    }
}
