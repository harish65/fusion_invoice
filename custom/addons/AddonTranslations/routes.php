<?php

Route::group(['prefix' => 'addontranslations', 'middleware' => ['web', 'auth.admin']], function ()
{
    Route::get('/', function()
    {
        // You can use the trans function to load strings from your addon translation files.
        // trans('AddonFolderName::translationfilename.language_key')
        // This will obey whatever language FI is running under.
        echo trans('AddonTranslations::translations.example_1');
    });
});