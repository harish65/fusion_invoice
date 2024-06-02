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

class AlterCustomFieldsForIsRequired extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        if(!Schema::hasColumn('custom_fields','is_required'))
        {
            Schema::table('custom_fields', function (Blueprint $table)
            {
                $table->boolean('is_required')->default(0)->after('display_order');
            });
        }
    }

}