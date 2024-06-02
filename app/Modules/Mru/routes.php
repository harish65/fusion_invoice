<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['prefix' => 'mru', 'middleware' => 'web', 'namespace' => 'FI\Modules\Mru\Controllers'], function () {
    Route::get('get-mru', ['uses' => 'MruController@getMru', 'as' => 'mru.load-ajax.data']);
});