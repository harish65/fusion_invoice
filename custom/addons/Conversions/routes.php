<?php

Route::group(['prefix' => 'conversions', 'middleware' => ['web','auth.admin'], 'namespace' => 'Addons\Conversions\Controllers'], function ()
{
    Route::get('/', ['uses' => 'ConversionController@index', 'as' => 'conversions.index']);
    Route::post('create', ['uses' => 'ConversionController@create', 'as' => 'conversions.create']);
    Route::post('compare', ['uses' => 'ConversionController@compare', 'as' => 'conversions.compare']);
    Route::post('truncate', ['uses' => 'ConversionController@truncateTables', 'as' => 'conversions.truncateTables']);
});