<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Providers;

use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        view()->composer('layouts.master', 'FI\Composers\LayoutComposer');
        view()->composer(['client_center.layouts.master', 'client_center.layouts.logged_in', 'client_center.layouts.public', 'layouts.master', 'setup.master','templates.invoices.default','templates.quotes.default','templates.payments.default','templates.emails.default','paymentcenter.paymentCenterLayouts.logged_in','paymentcenter.paymentCenterLayouts.master','paymentcenter.paymentCenterLayouts.payment_center_avatar','errors.link_expired','layouts._notification'], 'FI\Composers\SkinComposer');
        view()->composer(['clients._form', 'clients._settings'], 'FI\Composers\ClientFormComposer');
        view()->composer('reports.options.*', 'FI\Composers\ReportComposer');
        view()->composer('layouts.master', 'FI\Composers\MruComposer');
        view()->composer('layouts._head', 'FI\Composers\DateTimePickerComposer');
        view()->composer('tasks.widget.create_edit_modal', 'FI\Composers\DateTimePickerComposer');
        view()->composer('item_lookups._js_item_lookups.blade', 'FI\Composers\DateTimePickerComposer');
        view()->composer('dashboard.index.blade', 'FI\Composers\DateTimePickerComposer');
        view()->composer('settings.index.blade', 'FI\Composers\DateTimePickerComposer');
    }

    public function register()
    {
        //
    }
}
