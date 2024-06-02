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

class AlterClientsForSocialMediaUrlAndGeneralNotesAndLeadSourceNotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumns('clients', ['social_media_url','lead_source_notes','general_notes']))
        {
            Schema::table('clients', function (Blueprint $table)
            {
                $table->string('social_media_url')->nullable()->after('web');
                $table->text('lead_source_notes')->nullable()->after('important_note');
                $table->text('general_notes')->nullable()->after('important_note');
            });
        }
    }
}