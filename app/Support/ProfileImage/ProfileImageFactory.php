<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Support\ProfileImage;

use FI\Support\Directory;

class ProfileImageFactory
{
    public static function create()
    {
        $class = 'FI\Support\ProfileImage\Drivers\\' . config('fi.profileImageDriver');

        return new $class;
    }

    public static function getDrivers()
    {
        $driverFiles = Directory::listContents(app_path('Support/ProfileImage/Drivers'));
        $drivers     = [];

        foreach ($driverFiles as $driverFile)
        {
            $driver = str_replace('.php', '', $driverFile);

            $drivers[$driver] = $driver;
        }

        return $drivers;
    }
}