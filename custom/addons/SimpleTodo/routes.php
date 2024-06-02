<?php

Route::group(['middleware' => 'auth.admin', 'prefix' => 'simpletodo', 'namespace' => 'Addons\SimpleTodo\Controllers'], function ()
{
    Route::get('/', ['uses' => 'SimpleTodoController@index', 'as' => 'simpleTodo.index']);
    Route::get('create', ['uses' => 'SimpleTodoController@create', 'as' => 'simpleTodo.create']);
    Route::post('create', ['uses' => 'SimpleTodoController@store', 'as' => 'simpleTodo.store']);
    Route::get('{id}/edit', ['uses' => 'SimpleTodoController@edit', 'as' => 'simpleTodo.edit']);
    Route::post('{id}/edit', ['uses' => 'SimpleTodoController@update', 'as' => 'simpleTodo.update']);
    Route::get('{id}/delete', ['uses' => 'SimpleTodoController@delete', 'as' => 'simpleTodo.delete']);
});