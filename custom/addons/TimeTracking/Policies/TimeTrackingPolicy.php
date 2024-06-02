<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\TimeTracking\Policies;

use FI\Traits\Policy;

class TimeTrackingPolicy
{
    use Policy;

    private static $module = 'time_tracking';
}