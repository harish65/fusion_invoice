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

class AlterUsersForUserTypesDefault extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('users', 'user_type'))
        {
            DB::table('users')->whereNull('user_type')->update(['user_type' => 'admin']);
            DB::table('users')->where('user_type', '')->update(['user_type' => 'admin']);
        }
    }
}
