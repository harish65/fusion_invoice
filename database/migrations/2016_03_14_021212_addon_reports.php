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

class AddonReports extends Migration
{
    public function up()
    {
        Schema::table('addons', function (Blueprint $table)
        {
            $table->longText('navigation_reports')->nullable();
        });
    }
}
