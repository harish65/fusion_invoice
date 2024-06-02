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

class RenameGroupsToDocumentNumberSchemes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable( 'groups'))
        {
            Schema::rename('groups', 'document_number_schemes');
        }

        if (!Schema::hasColumn('invoices', 'document_number_scheme_id'))
        {
            DB::statement("ALTER TABLE " . DB::getTablePrefix() . "invoices CHANGE group_id document_number_scheme_id INT(11) NOT NULL");
        }
        if (!Schema::hasColumn('quotes', 'document_number_scheme_id'))
        {
            DB::statement("ALTER TABLE " . DB::getTablePrefix() . "quotes CHANGE group_id document_number_scheme_id INT(11) NOT NULL");
        }
        if (!Schema::hasColumn('recurring_invoices', 'document_number_scheme_id'))
        {
            DB::statement("ALTER TABLE " . DB::getTablePrefix() . "recurring_invoices CHANGE group_id document_number_scheme_id INT(11) NOT NULL");
        }
    }

}
