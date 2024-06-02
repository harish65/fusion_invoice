<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\TimeTracking\Reports;

use Addons\TimeTracking\Models\TimeTrackingTimer;
use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Support\CurrencyFormatter;
use FI\Support\DateFormatter;
use FI\Support\NumberFormatter;

class TimesheetReport
{
    public function getResults($fromDate, $toDate, $companyProfileId = null, $status = null)
    {
        $results = [
            'from_date'       => DateFormatter::format($fromDate),
            'to_date'         => DateFormatter::format($toDate),
            'hours_unbilled'  => 0,
            'hours_billed'    => 0,
            'hours_total'     => 0,
            'amount_unbilled' => 0,
            'amount_billed'   => 0,
            'amount_total'    => 0,
            'projects'        => [],
        ];

        if ($companyProfileId)
        {
            $results['company_profile'] = CompanyProfile::find($companyProfileId)->name;
        }
        else
        {
            $results['company_profile'] = trans('fi.all_company_profiles');
        }

        $timers = TimeTrackingTimer::with(['task', 'task.project' => function ($query) use ($companyProfileId, $status)
        {
            if ($companyProfileId)
            {
                $query->where('company_profile_id', $companyProfileId);
            }
            if ($status)
            {
                $query->where('status', $status);
            }
        }, 'task.project.client'])
            ->where([
                ['start_at', '>=', $fromDate],
                ['end_at', '<=', $toDate],
            ])
            ->orderBy('created_at')
            ->get();

        foreach ($timers as $timer)
        {
            $task    = $timer->task;
            $project = $task->project;
            if ($project)
            {
                $results['projects'][$project->id]['name']        = $project->name;
                $results['projects'][$project->id]['hourly_rate'] = $project->formatted_hourly_rate;
                $results['projects'][$project->id]['client']      = $project->client->name;
                $results['projects'][$project->id]['status']      = $project->status;

                $results['projects'][$project->id]['tasks'][$task->id]['name']   = $task->name;
                $results['projects'][$project->id]['tasks'][$task->id]['billed'] = $task->billed;
                $results['projects'][$project->id]['tasks'][$task->id]['hours']  = NumberFormatter::format($task->hours);

                $results['projects'][$project->id]['tasks'][$task->id]['timers'][$timer->id]['start_at'] = $timer->formatted_start_at;
                $results['projects'][$project->id]['tasks'][$task->id]['timers'][$timer->id]['end_at']   = $timer->formatted_end_at;
                $results['projects'][$project->id]['tasks'][$task->id]['timers'][$timer->id]['hours']    = $timer->hours;
                $results['projects'][$project->id]['tasks'][$task->id]['timers'][$timer->id]['amount']   = CurrencyFormatter::format($timer->hours * $project->hourly_rate);

                $results['hours_total']  = ($results['hours_total'] += $timer->hours);
                $results['amount_total'] = ($results['amount_total'] += ($timer->hours * $project->hourly_rate));

                $results['projects'][$project->id]['hours_total']  = (isset($results['projects'][$project->id]['hours_total'])) ? $results['projects'][$project->id]['hours_total'] + $timer->hours : $timer->hours;
                $results['projects'][$project->id]['amount_total'] = (isset($results['projects'][$project->id]['amount_total'])) ? $results['projects'][$project->id]['amount_total'] + ($timer->hours * $project->hourly_rate) : ($timer->hours * $project->hourly_rate);

                if ($task->billed)
                {
                    $results['projects'][$project->id]['hours_billed']  = (isset($results['projects'][$project->id]['hours_billed'])) ? $results['projects'][$project->id]['hours_billed'] + $timer->hours : $timer->hours;
                    $results['projects'][$project->id]['amount_billed'] = (isset($results['projects'][$project->id]['amount_billed'])) ? $results['projects'][$project->id]['amount_billed'] + ($timer->hours * $project->hourly_rate) : ($timer->hours * $project->hourly_rate);

                    $results['hours_billed']  = ($results['hours_billed'] += $timer->hours);
                    $results['amount_billed'] = ($results['amount_billed'] += ($timer->hours * $project->hourly_rate));
                }
                else
                {
                    $results['projects'][$project->id]['hours_unbilled']  = (isset($results['projects'][$project->id]['hours_unbilled'])) ? $results['projects'][$project->id]['hours_unbilled'] + $timer->hours : $timer->hours;
                    $results['projects'][$project->id]['amount_unbilled'] = (isset($results['projects'][$project->id]['amount_unbilled'])) ? $results['projects'][$project->id]['amount_unbilled'] + ($timer->hours * $project->hourly_rate) : ($timer->hours * $project->hourly_rate);

                    $results['hours_unbilled']  = ($results['hours_unbilled'] += $timer->hours);
                    $results['amount_unbilled'] = ($results['amount_unbilled'] += ($timer->hours * $project->hourly_rate));
                }
            }

        }
        foreach ($results['projects'] as $key => $project)
        {
            $results['projects'][$key]['hours_unbilled']  = NumberFormatter::format(isset($project['hours_unbilled']) ? $project['hours_unbilled'] : 0.00);
            $results['projects'][$key]['hours_billed']    = NumberFormatter::format(isset($project['hours_billed']) ? $project['hours_billed'] : 0.00);
            $results['projects'][$key]['hours_total']     = NumberFormatter::format(isset($project['hours_total']) ? $project['hours_total'] : 0.00);
            $results['projects'][$key]['amount_unbilled'] = CurrencyFormatter::format(isset($project['amount_unbilled']) ? $project['amount_unbilled'] : 0.00);
            $results['projects'][$key]['amount_billed']   = CurrencyFormatter::format(isset($project['amount_billed']) ? $project['amount_billed'] : 0.00);
            $results['projects'][$key]['amount_total']    = CurrencyFormatter::format(isset($project['amount_total']) ? $project['amount_total'] : 0.00);
        }
        $results['hours_unbilled']  = NumberFormatter::format($results['hours_unbilled']);
        $results['hours_billed']    = NumberFormatter::format($results['hours_billed']);
        $results['hours_total']     = NumberFormatter::format($results['hours_total']);
        $results['amount_unbilled'] = CurrencyFormatter::format($results['amount_unbilled']);
        $results['amount_billed']   = CurrencyFormatter::format($results['amount_billed']);
        $results['amount_total']    = CurrencyFormatter::format($results['amount_total']);

        return $results;
    }
}