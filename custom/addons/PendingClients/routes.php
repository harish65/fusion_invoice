<?php

Route::group(['middleware' => 'auth.admin', 'prefix' => 'pending_clients', 'namespace' => 'Addons\PendingClients\Controllers'], function ()
{
    Route::get('/', ['uses' => 'PendingClientController@index', 'as' => 'pendingClients.index']);
    Route::get('{id}/keep', ['uses' => 'PendingClientController@keep', 'as' => 'pendingClients.keep']);
    Route::get('{id}/delete', ['uses' => 'PendingClientController@delete', 'as' => 'pendingClients.delete']);
});