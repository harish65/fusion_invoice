<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'prefix' => 'site_bridge', 'namespace' => 'Addons\SiteBridge\Controllers'], function ()
{
    Route::get('/', ['uses' => 'SiteBridgeController@index', 'as' => 'siteBridge.index']);
    Route::post('/', ['uses' => 'SiteBridgeController@import', 'as' => 'siteBridge.import']);
});