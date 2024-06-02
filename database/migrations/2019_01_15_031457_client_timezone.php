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

class ClientTimezone extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('clients', 'timezone'))
        {
            Schema::table('clients', function (Blueprint $table) {
                $table->string('timezone')->nullable();
            });
        }
    }
}
