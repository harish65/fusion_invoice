<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Users\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CompanyProfilesCreate extends Migration
{
    public function up()
    {
        $maxUserId = Invoice::select('user_id')
            ->orderBy(DB::raw('count(*)'), 'desc')
            ->groupBy('user_id')
            ->whereIn('user_id', function ($query)
            {
                $query->select('id')->from('users')->where('client_id', 0);
            })
            ->first();

        if ($maxUserId)
        {
            DB::table('expenses')->where('user_id', 0)->update(['user_id' => $maxUserId->user_id]);
        }

        foreach (User::withTrashed()->where('client_id', 0)->get() as $user)
        {
            if (count($user->invoices) or count($user->quotes) or count($user->expenses))
            {
                CompanyProfile::flushEventListeners();
                $companyProfile = CompanyProfile::firstOrNew(['company' => ($user->company) ?: $user->name]);

                if (!$companyProfile->id)
                {
                    $companyProfile->fill([
                        'address' => $user->address,
                        'city'    => $user->city,
                        'state'   => $user->state,
                        'zip'     => $user->zip,
                        'country' => $user->country,
                        'phone'   => $user->phone,
                        'fax'     => $user->fax,
                        'mobile'  => $user->mobile,
                        'web'     => $user->web,
                    ]);

                    $companyProfile->save();
                }

                DB::table('invoices')->where('user_id', $user->id)->update(['company_profile_id' => $companyProfile->id]);
                DB::table('quotes')->where('user_id', $user->id)->update(['company_profile_id' => $companyProfile->id]);
                DB::table('expenses')->where('user_id', $user->id)->update(['company_profile_id' => $companyProfile->id]);
            }
        }
    }
}
