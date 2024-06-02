<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\Notifications\Controllers'], function ()
{
    Route::get('notifications/user-notifications', ['uses' => 'NotificationController@userNotifications', 'as' => 'notifications.userNotifications']);
    Route::post('notifications/mark-viewed/{notification}', ['uses' => 'NotificationController@markViewed', 'as' => 'notifications.markViewed']);
    Route::post('notifications/clear-all', ['uses' => 'NotificationController@markAllViewed', 'as' => 'notifications.clearAll']);
    Route::get('get-notification', ['uses' => 'NotificationController@getNotification', 'as' => 'notification.load-ajax.data']);
});