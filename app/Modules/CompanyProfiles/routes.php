<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group(['middleware' => ['web', 'auth.admin'], 'namespace' => 'FI\Modules\CompanyProfiles\Controllers'], function ()
{
    Route::group(['prefix' => 'company_profiles'], function ()
    {
        Route::get('/', ['uses' => 'CompanyProfileController@index', 'as' => 'company.profiles.index'])->middleware('can:company_profiles.view');
        Route::get('create', ['uses' => 'CompanyProfileController@create', 'as' => 'company.profiles.create'])->middleware('can:company_profiles.create');
        Route::get('{id}/edit', ['uses' => 'CompanyProfileController@edit', 'as' => 'company.profiles.edit'])->middleware('can:company_profiles.update');
        Route::get('{id}/delete', ['uses' => 'CompanyProfileController@delete', 'as' => 'company.profiles.delete'])->middleware('can:company_profiles.delete');

        Route::post('store', ['uses' => 'CompanyProfileController@store', 'as' => 'company.profiles.store'])->middleware('can:company_profiles.create');
        Route::post('update/{id}', ['uses' => 'CompanyProfileController@update', 'as' => 'company.profiles.update'])->middleware('can:company_profiles.update');

        Route::post('{id}/delete_logo', ['uses' => 'CompanyProfileController@deleteLogo', 'as' => 'company.profiles.deleteLogo'])->middleware('can:company_profiles.update');
        Route::post('custom_field/{id?}/delete_image/{field_name?}', ['uses' => 'CompanyProfileController@deleteImage', 'as' => 'company.profiles.deleteImage'])->middleware('can:company_profiles.update');

        Route::post('delete/confirmation/modal', ['uses' => 'CompanyProfileController@deleteModal', 'as' => 'company.profiles.delete.modal'])->middleware('can:company_profiles.delete');

        Route::group(['middleware' => ['check.invoiceStatus']], function () {
            Route::post('ajax/modal_lookup', ['uses' => 'CompanyProfileController@ajaxModalLookup', 'as' => 'company.profiles.ajax.modalLookup'])->middleware('can:company_profiles.view');
        });
    });
});

Route::get('company_profiles/{id}/logo', ['uses' => 'FI\Modules\CompanyProfiles\Controllers\LogoController@logo', 'as' => 'company.profiles.logo'])->middleware('can:company_profiles.view');