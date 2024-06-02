<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class PaymentCenterInstall extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('paymentcenter_users'))
        {
            Schema::create('paymentcenter_users', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();
                $table->string('name');
                $table->string('username');
                $table->string('password');
                $table->rememberToken();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('paymentcenter_users'))
        {
            Schema::drop('paymentcenter_users');
        }
    }
}
