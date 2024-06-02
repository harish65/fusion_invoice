<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\Functions;

use Illuminate\Support\Facades\File;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class AddonServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        foreach (File::allFiles(addon_path('Functions/Functions')) as $helper)
        {
            \Log::info('Requiring ' . $helper);
            require_once $helper->getPathname();
        }
    }
}
