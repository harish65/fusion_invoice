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
use FI\Modules\Reports\Reports\RevenueByClientReport;
use FI\Modules\Reports\Requests\DateRangeRequest;
use FI\Support\DateFormatter;
use FI\Support\PDF\PDFFactory;
use Illuminate\Support\Facades\Log;
use Throwable;

class RevenueByClientReportController extends Controller
{
    private $report;

    public function __construct(RevenueByClientReport $report)
    {
        $this->report = $report;
    }

    public function index()
    {
        $sort_option = [
            'client_name'    => trans('fi.filter_by_client_name'),
            'total_payments' => trans('fi.total_payments'),
        ];
        return view('reports.options.revenue_by_client')
            ->with('sort_option', $sort_option);
    }

    public function validateOptions(DateRangeRequest $request)
    {

    }

    public function html()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'revenue_by_client', 'title' => trans('fi.revenue_by_client'), 'id' => null]));

        $results = $this->report->getResults(request('from_date'), request('to_date'),
            request('company_profile_id'), request('sort_option'));

        $months = [];

        foreach (range(1, 12) as $month)
        {
            $months[$month] = DateFormatter::getMonthShortName($month);
        }

        return view('reports.output.revenue_by_client')
            ->with('results', $results)
            ->with('months', $months);
    }

    public function pdf()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'revenue_by_client', 'title' => trans('fi.revenue_by_client'), 'id' => null]));

        $pdf = PDFFactory::create();
        $pdf->setPaperOrientation('landscape');

        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('sort_option')
        );

        $months = [];

        foreach (range(1, 12) as $month)
        {
            $months[$month] = DateFormatter::getMonthShortName($month);
        }

        try
        {
            $html = view('reports.output.revenue_by_client')
                ->with('results', $results)
                ->with('months', $months)
                ->render();
            $html = (str_replace(iframeThemeColor(), '', $html));
            $pdf->download($html, trans('fi.revenue_by_client') . '.pdf');
        }
        catch (Throwable $e)
        {
            Log::error($e->getMessage());
        }

    }

    public function csv()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'revenue_by_client', 'title' => trans('fi.revenue_by_client'), 'id' => null]));

        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('sort_option')
        );

        $months = $columns = [];

        foreach (range(1, 12) as $month)
        {
            $months[$month] = DateFormatter::getMonthShortName($month);
        }

        try
        {
            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=" . trans('fi.revenue_by_client') . '.csv',
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $label[] = trans('fi.client');
            $label[] = trans('fi.year');
            foreach ($months as $month)
            {
                $label[] = $month;
            }
            $label[] = trans('fi.total');

            foreach ($results['clients'] as $client)
            {
                $data   = [];
                $data[] = $client['client'];
                $data[] = $client['year'];
                foreach (array_keys($client['months']) as $key => $monthKey)
                {
                    $data[] = revenueByClientCurrencyFormatter($client['months'][$monthKey]);
                }
                $data[]    = revenueByClientCurrencyFormatter($client['total']);
                $columns[] = $data;
            }

            $callback = function () use ($results, $label, $columns)
            {
                $file = fopen('php://output', 'w');

                fputcsv($file, ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '']);

                fputcsv($file, [$label['0'], $label['1'], $label['2'], $label['3'], $label['4'], $label['5'], $label['6'], $label['7'], $label['8'], $label['9'], $label['10'], $label['11'], $label['12'], $label['13'], $label['14']]);
                foreach ($columns as $column)
                {
                    fputcsv($file, [$column['0'], $column['1'], $column['2'], $column['3'], $column['4'], $column['5'], $column['6'], $column['7'], $column['8'], $column['9'], $column['10'], $column['11'], $column['12'], $column['13'], $column['14']]);
                }

                fputcsv($file, ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '']);
                fputcsv($file, [trans('fi.total'), '', '', '', '', '', '', '', '', '', '', '', '', '', $results['grand_total']]);

                fclose($file);

            };

            return response()->streamDownload($callback, trans('fi.revenue_by_client') . '.csv', $headers);
        }
        catch (Throwable $e)
        {
            Log::error($e->getMessage());
        }

    }
}