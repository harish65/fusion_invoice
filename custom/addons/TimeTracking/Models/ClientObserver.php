<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\TimeTracking\Models;

class ClientObserver
{
    public function deleted($client)
    {
        $projects = TimeTrackingProject::where('client_id', $client->id)->get();

        foreach ($projects as $project)
        {
            $project->delete();
        }
    }
}