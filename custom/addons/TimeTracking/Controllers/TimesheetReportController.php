<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\TimeTracking\Controllers;

use Addons\TimeTracking\ProjectStatuses;
use Addons\TimeTracking\Reports\TimesheetReport;
use FI\Http\Controllers\Controller;
use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\Reports\Requests\DateRangeRequest;
use FI\Support\PDF\PDFFactory;

class TimesheetReportController extends Controller
{
    private $timesheetReport;

    public function __construct(TimesheetReport $timesheetReport)
    {
        $this->timesheetReport = $timesheetReport;
    }

    public function index()
    {
        return view('time_tracking.reports.options.timesheet')
            ->with('companyProfiles', ['' => trans('fi.all_company_profiles')] + CompanyProfile::getList())
            ->with('statuses', ['' => trans('fi.all_statuses')] + ProjectStatuses::lists());
    }

    public function ajaxValidate(DateRangeRequest $request)
    {

    }

    public function html()
    {
        $results = $this->timesheetReport->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('status')
        );

        return view('time_tracking.reports.output.timesheet')
            ->with('results', $results);
    }

    public function pdf()
    {
        $pdf = PDFFactory::create();

        $results = $this->timesheetReport->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('status')
        );

        $html = view('time_tracking.reports.output.timesheet')
            ->with('results', $results)->render();
        $html = (str_replace(iframeThemeColor(), '', $html));
        $pdf->download($html, trans('TimeTracking::lang.timesheet') . '.pdf');
    }

    public function csv()
    {
        $results = $this->timesheetReport->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('status')
        );

        try
        {

            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=" . trans('TimeTracking::lang.timesheet') . '.csv',
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $columns = [
                trans('TimeTracking::lang.task'),
                trans('TimeTracking::lang.start_time'),
                trans('TimeTracking::lang.stop_time'),
                trans('TimeTracking::lang.unbilled_hours'),
                trans('TimeTracking::lang.billed_hours')
            ];

            $callback = function () use ($results, $columns)
            {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['', '', $results['company_profile'], '', '']);
                fputcsv($file, ['', $results['from_date'], '', $results['to_date'], '']);
                fputcsv($file, ['', '', '', '', '']);

                foreach ($results['projects'] as $project)
                {
                    fputcsv($file, $columns);

                    foreach ($project['tasks'] as $task)
                    {
                        foreach ($task['timers'] as $timer)
                        {
                            $name         = $task['name'];
                            $start_at     = $timer['start_at'];
                            $end_at       = $timer['end_at'];
                            $unbill_hours = !$task['billed'] ? $timer['hours'] : '';
                            $bill_hours   = $task['billed'] ? $timer['hours'] : '';

                            fputcsv($file, [$name, $start_at, $end_at, $unbill_hours, $bill_hours]);
                        }
                    }
                    fputcsv($file, ['', '', '', '', '']);
                    fputcsv($file, ['', '', trans('fi.total'), $project['hours_unbilled'], $project['hours_billed']]);
                }

                fclose($file);
            };

            return response()->streamDownload($callback, trans('TimeTracking::lang.timesheet') . '.csv', $headers);
        }
        catch (Throwable $e)
        {
            Log::error($e->getMessage());
        }

    }
}