<?php

Route::group(['middleware' => 'auth.admin', 'prefix' => 'languagechecker', 'namespace' => 'Addons\LanguageChecker\Controllers'], function ()
{
    Route::get('/', ['uses' => 'LanguageCheckerController@index', 'as' => 'languageChecker.index']);
});