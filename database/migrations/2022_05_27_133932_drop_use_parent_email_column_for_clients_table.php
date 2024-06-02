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

class DropUseParentEmailColumnForClientsTable extends Migration
{
    public function up()
    {
        if (Schema::hasColumns('clients', ['email_default','use_parent_email']))
        {
            DB::table('clients')->where('use_parent_email', 1)->update(['email_default' => 'use_parent_email']);
        }

        if (Schema::hasColumn('clients', 'use_parent_email'))
        {
            Schema::table('clients', function (Blueprint $table)
            {
                $table->dropColumn('use_parent_email');
            });
        }
    }
}
