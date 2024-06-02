<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use FI\Modules\Settings\Models\UserSetting;
use FI\Modules\Users\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Output\ConsoleOutput;

class ClientsDefaultSettings extends Migration
{

    public function up()
    {

        $users  = DB::table('users')->whereNotIn('user_type', ['client', 'system'])->get();
        $output = new ConsoleOutput();

        try
        {
            if ($users != null)
            {
                $client_list_columns = User::clientColumnSettings();
                foreach ($users as $user)
                {
                    foreach ($client_list_columns as $key => $index)
                    {
                        userSetting::saveByKey('clientColumnSettings' . $key, 1, $user);
                    }
                }
            }
        }
        catch (\Exception $e)
        {
            $output->writeln($e->getMessage());
        }
    }

}