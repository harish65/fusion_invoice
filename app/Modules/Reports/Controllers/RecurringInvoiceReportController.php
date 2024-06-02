<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Reports\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Mru\Events\MruLog;
use FI\Modules\Reports\Reports\RecurringInvoiceReport;
use FI\Modules\Reports\Requests\DateRangeRequest;
use FI\Support\PDF\PDFFactory;
use Illuminate\Support\Facades\Log;
use Throwable;

class RecurringInvoiceReportController extends Controller
{
    private $report;

    public function __construct(RecurringInvoiceReport $report)
    {
        $this->report = $report;
    }

    public function index()
    {
        return view('reports.options.recurring_invoice');
    }

    public function validateOptions(DateRangeRequest $request)
    {

    }

    public function html()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'recurring_invoice_list', 'title' => trans('fi.recurring_invoice_list'), 'id' => null]));

        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id')
        );

        return view('reports.output.recurring_invoice')
            ->with('results', $results);
    }

    public function pdf()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'recurring_invoice_list', 'title' => trans('fi.recurring_invoice_list'), 'id' => null]));

        $pdf = PDFFactory::create();

        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id')
        );

        try
        {
            $html = view('reports.output.recurring_invoice')
                ->with('results', $results)->render();
            $html = (str_replace(iframeThemeColor(), '', $html));
            $pdf->download($html, trans('fi.recurring_invoice') . '.pdf');
        }
        catch (Throwable $e)
        {
            Log::error($e->getMessage());
        }
    }


    public function csv()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'recurring_invoice_list', 'title' => trans('fi.recurring_invoice_list'), 'id' => null]));

        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id')
        );

        try
        {
            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=" . trans('fi.recurring_invoice') . '.csv',
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $columns = [
                trans('fi.id'),
                trans('fi.client'),
                trans('fi.summary'),
                trans('fi.next_date'),
                trans('fi.stop_date'),
                trans('fi.total')
            ];

            $callback = function () use ($results, $columns)
            {
                $file = fopen('php://output', 'w');

                fputcsv($file, ['', $results['from_date'], '', $results['to_date'], '', '']);
                fputcsv($file, ['', '', '', '', '', '']);
                if (count($results['records']) > 0)
                {
                    foreach ($results['records'] as $period => $period_wise_data)
                    {
                        foreach ($period_wise_data as $frequency => $frequency_wise_data)
                        {
                            fputcsv($file, ['', '', trans('fi.every') . ' ' . $frequency . ' ' . $period, '', '', '']);
                            fputcsv($file, $columns);
                            foreach ($frequency_wise_data as $item)
                            {
                                fputcsv($file, [
                                    $item['id'],
                                    $item['client_name'],
                                    $item['summary'],
                                    $item['next_date'],
                                    $item['stop_date'],
                                    $item['total'],
                                ]);
                            }
                            fputcsv($file, ['', '', '', trans('fi.total'), trans('fi.invoices') . ' ' . $results['total_invoice'][$period][$frequency], $results['total_amount'][$period][$frequency]]);
                        }
                    }
                    fputcsv($file, ['', '', '', '', '', '']);
                    fputcsv($file, ['', '', '', trans('fi.report_total'), $results['grand_total_invoice'], $results['grand_total_amount']]);
                }

                fclose($file);

            };

            return response()->streamDownload($callback, trans('fi.recurring_invoice') . '.csv', $headers);
        }
        catch (Throwable $e)
        {
            Log::error($e->getMessage());
        }

    }
}