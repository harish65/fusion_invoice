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

class AddPreviousPriceForQuote extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('quote_items', 'previous_price'))
        {
            Schema::table('quote_items', function (Blueprint $table)
            {
                $table->decimal('previous_price', 20, 4)->default(0.00);
            });
        }
    }
}