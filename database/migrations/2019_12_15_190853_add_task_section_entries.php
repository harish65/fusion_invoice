<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class AddTaskSectionEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('task_section'))
        {
            if (DB::table('task_section')->count() == 0)
            {
                DB::statement('INSERT INTO ' . DB::getTablePrefix() . 'task_section (id, name, slug) VALUES(1, \'New\', \'new\'),(2, \'Today\', \'today\'),(3, \'Tomorrow\', \'tomorrow\'),(4, \'Later\', \'later\');');
            }
        }
    }
}
