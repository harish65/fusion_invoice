<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\Seeder\Controllers'], function ()
{
    Route::get('data/seeder', ['uses' => 'SeederController@index', 'as' => 'data.seeder']);
    Route::post('data-seeder-modal', ['uses' => 'SeederController@dataSeederModal', 'as' => 'data.seeder.modal']);
});