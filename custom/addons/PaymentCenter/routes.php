<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Addons\PaymentCenter\Middleware\AuthenticatePaymentCenter;

Route::group(['prefix' => 'payctr', 'namespace' => 'Addons\PaymentCenter\Controllers'], function ()
{

    Route::group(['middleware' => 'web'], function ()
    {

        Route::get('login', ['uses' => 'PaymentCenterSessionController@login', 'as' => 'paymentCenter.login']);
        Route::post('login', ['uses' => 'PaymentCenterSessionController@attempt', 'as' => 'paymentCenter.login.attempt']);
        Route::get('logout', ['uses' => 'PaymentCenterSessionController@logout', 'as' => 'paymentCenter.logout']);

        Route::group(['middleware' => 'auth.admin'], function ()
        {
            Route::get('users', ['uses' => 'PaymentCenterUserController@index', 'as' => 'paymentCenter.users.index']);
            Route::get('users/create', ['uses' => 'PaymentCenterUserController@create', 'as' => 'paymentCenter.users.create']);
            Route::post('users/create', ['uses' => 'PaymentCenterUserController@store', 'as' => 'paymentCenter.users.store']);
            Route::get('users/{user}/edit', ['uses' => 'PaymentCenterUserController@edit', 'as' => 'paymentCenter.users.edit']);
            Route::post('users/{user}/edit', ['uses' => 'PaymentCenterUserController@update', 'as' => 'paymentCenter.users.update']);
            Route::get('users/{user}/delete', ['uses' => 'PaymentCenterUserController@delete', 'as' => 'paymentCenter.users.delete']);
            Route::get('users/{user}/password/edit', ['uses' => 'PaymentCenterUserController@editPassword', 'as' => 'paymentCenter.users.password.edit']);
            Route::post('users/{user}/password/edit', ['uses' => 'PaymentCenterUserController@updatePassword', 'as' => 'paymentCenter.users.password.update']);
        });

        Route::group(['middleware' => AuthenticatePaymentCenter::class], function ()
        {
            Route::get('/', ['uses' => 'PaymentCenterController@dashboard', 'as' => 'paymentCenter.dashboard']);
            Route::post('/', ['uses' => 'PaymentCenterController@search', 'as' => 'paymentCenter.search']);
        });
    });

});