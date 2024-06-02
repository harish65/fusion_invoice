<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\Conversions\Support;

use FI\Support\Directory;

class ConversionFactory
{
    public static function create($driver)
    {
        $class = 'Addons\Conversions\Support\Drivers\\' . $driver;

        return app($class);
    }

    public static function getDrivers()
    {
        $driverFiles = Directory::listContents(addon_path('Conversions/Support/Drivers'));
        $drivers     = [];

        foreach ($driverFiles as $driverFile)
        {
            $driver = str_replace('.php', '', $driverFile);

            $drivers[$driver] = $driver;
        }

        return $drivers;
    }
}