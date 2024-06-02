<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\TimeTracking;

use Addons\TimeTracking\Models\ClientObserver;
use Addons\TimeTracking\Models\InvoiceObserver;
use Addons\TimeTracking\Models\TimeTrackingProject;
use Addons\TimeTracking\Models\TimeTrackingProjectObserver;
use Addons\TimeTracking\Models\TimeTrackingTask;
use Addons\TimeTracking\Models\TimeTrackingTaskObserver;
use FI\Modules\Clients\Models\Client;
use FI\Modules\Invoices\Models\Invoice;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class AddonServiceProvider extends ServiceProvider
{
    protected $subscribe = [
        'Addons\TimeTracking\EventSubscriber',
    ];

    public function boot()
    {
        Client::observe(ClientObserver::class);
        Invoice::observe(InvoiceObserver::class);
        TimeTrackingProject::observe(TimeTrackingProjectObserver::class);
        TimeTrackingTask::observe(TimeTrackingTaskObserver::class);
    }
}
