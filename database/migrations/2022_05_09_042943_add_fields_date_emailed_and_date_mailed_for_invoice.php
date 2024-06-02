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

class AddFieldsDateEmailedAndDateMailedForInvoice extends Migration
{

    public function up()
    {
        if (!Schema::hasColumns('invoices', ['date_emailed','date_mailed']))
        {
            Schema::table('invoices', function (Blueprint $table)
            {
                $table->date('date_emailed')->nullable();
                $table->date('date_mailed')->nullable();
            });
        }
    }

}
