<?php

Route::group(['before' => 'auth', 'prefix' => 'pending_clients'], function()
{
    Route::get('/', ['uses' => 'PendingClientController@index', 'as' => 'pendingClients.index']);
    Route::get('{id}/keep', ['uses' => 'PendingClientController@keep', 'as' => 'pendingClients.keep']);
    Route::get('{id}/delete', ['uses' => 'PendingClientController@delete', 'as' => 'pendingClients.delete']);
});