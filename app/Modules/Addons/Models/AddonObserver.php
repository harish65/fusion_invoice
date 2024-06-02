<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Addons\Models;

use Addons\TimeTracking\Models\TimeTrackingTimer;
use FI\Modules\Settings\Models\UserSetting;
use Illuminate\Support\Facades\Session;

class AddonObserver
{

    public function saved(Addon $addon)
    {
        if ($addon->path == 'TimeTracking')
        {
            UserSetting::saveByKey('floatingTimeTrackingAddon', 1, auth()->user());
        }
    }

    public function deleted(Addon $addon)
    {
        if ($addon->path == 'TimeTracking')
        {
            $timeTrackingTimers = TimeTrackingTimer::where('end_at', '0000-00-00')->get();
            if ($timeTrackingTimers->count() > 0)
            {
                foreach ($timeTrackingTimers as $timeTrackingTimer)
                {
                    $timeTrackingTimer->end_at = date('Y-m-d H:i:s');
                    $timeTrackingTimer->save();
                }
            }

            UserSetting::saveByKey('floatingTimeTrackingAddon', 0, auth()->user());
            Session::remove('timeTracker');
        }
    }
}
