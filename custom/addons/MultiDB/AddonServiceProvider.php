<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\MultiDB;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class AddonServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Event::listen('LoginAttempt', function ($email)
        {
            $profiles = require addon_path('MultiDB' . '/profiles.php');

            if (isset($profiles[$email]))
            {
                session(['multidb_profile' => $profiles[$email]]);
            }
            else
            {
                session(['multidb_profile' => config('database.default')]);
            }
        });

        //  dd(session('multidb_profile'));

        if (Session::has('multidb_profile'))
        {
            dd('YES');
            config(['database.default' => session('multidb_profile')]);
        }
    }

    public function register()
    {
        //
    }
}
