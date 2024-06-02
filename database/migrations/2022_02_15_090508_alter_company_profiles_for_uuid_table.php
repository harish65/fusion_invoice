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
use Illuminate\Support\Str;

class AlterCompanyProfilesForUuidTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        if(!Schema::hasColumn('company_profiles','uuid'))
        {
            Schema::table('company_profiles', function (Blueprint $table)
            {
                $table->uuid('uuid')->after('id');
            });
        }

        $company_profiles = DB::table('company_profiles')->get();

        foreach ($company_profiles as $company_profile)
        {
            DB::table('company_profiles')
              ->where('id', $company_profile->id)
              ->update(['uuid' => Str::uuid()]);
        }
    }
}