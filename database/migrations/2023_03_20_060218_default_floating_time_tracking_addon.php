<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use FI\Modules\Addons\Models\Addon;
use FI\Modules\Settings\Models\UserSetting;
use FI\Modules\Users\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {

    public function up()
    {
        if (Addon::whereName('Time Tracking')->count() > 0)
        {
            $users = User::whereIn('user_type', ['admin', 'standard_user'])->get();

            if (count($users) > 0)
            {
                foreach ($users as $user)
                {
                    if (UserSetting::whereUserId($user->id)->whereSettingKey('floatingTimeTrackingAddon')->count() == 0)
                    {
                        UserSetting::saveByKey('floatingTimeTrackingAddon', 1, $user);
                    }
                }
            }
        }
    }
};
