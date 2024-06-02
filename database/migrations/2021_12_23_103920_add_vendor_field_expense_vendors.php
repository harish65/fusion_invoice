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

class AddVendorFieldExpenseVendors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumns('expense_vendors',['email','mobile','contact_names','address','notes']))
        {
            Schema::table('expense_vendors', function (Blueprint $table)
            {
                $table->string('email')->nullable()->after('name');
                $table->string('mobile')->nullable()->after('email');
                $table->string('contact_names')->nullable()->after('mobile');
                $table->string('address')->nullable()->after('contact_names');
                $table->string('notes')->nullable()->after('address');
            });
        }
    }

}
