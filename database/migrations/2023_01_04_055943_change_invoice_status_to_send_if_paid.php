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

return new class extends Migration
{
    public function up()
    {
        DB::table('invoices')
            ->join('invoice_amounts', function ($join)
            {
                $join->on('invoices.id', '=', 'invoice_amounts.invoice_id');
                $join->where('invoice_amounts.total', '!=', 0);
                $join->where('invoice_amounts.balance', '<=', 0);
            })
            ->where('invoices.status', 'draft')
            ->update(['invoices.status' => 'sent']);
    }
};