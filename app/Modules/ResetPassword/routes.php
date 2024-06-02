<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web'], 'namespace' => 'FI\Modules\ResetPassword\Controllers'], function ()
{
    Route::get('resetpassword', ['uses' => 'ResetPasswordController@index', 'as' => 'resetPassword.index']);
    Route::post('resetpassword', ['uses' => 'ResetPasswordController@update', 'as' => 'resetPassword.update']);

    Route::get('resetpassword/success', ['uses' => 'ResetPasswordController@success', 'as' => 'resetPassword.success']);
});