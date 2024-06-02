<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeFieldToInvoiceTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('invoices', function (Blueprint $table)
        {
            if (!Schema::hasColumn('invoices', 'type'))
            {
                $table->enum('type', ['invoice', 'credit_memo'])->default('invoice')->after('status');
            }

            if (Schema::hasColumn('invoices', 'status'))
            {
                DB::statement("ALTER TABLE " . DB::getTablePrefix() . "invoices MODIFY status ENUM('draft', 'sent', 'viewed','paid', 'canceled', 'unpaid', 'overdue','mailed','applied')");
            }

            if (DB::table('document_number_schemes')->whereName('Credit Memo Default')->count() == 0)
            {
                DB::table('document_number_schemes')
                    ->insert([
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'name'       => 'Credit Memo Default',
                        'format'     => '{INVOICE_PREFIX} CR{YEAR}{NUMBER}',
                    ]);
            }
        });


        Schema::table('payments', function ($table)
        {
            if (!Schema::hasColumn('payments', 'credit_memo_id'))
            {
                $table->integer('credit_memo_id')->nullable();
            }
            if (Schema::hasColumn('payments', 'payment_method_id'))
            {
                $table->integer('payment_method_id')->nullable()->change();
            }
        });
    }
}

?>
