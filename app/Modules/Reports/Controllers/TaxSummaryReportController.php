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
use FI\Modules\Reports\Reports\TaxSummaryReport;
use FI\Modules\Reports\Requests\DateRangeRequest;
use FI\Support\PDF\PDFFactory;
use Illuminate\Support\Facades\Log;
use Throwable;

class TaxSummaryReportController extends Controller
{
    private $report;

    public function __construct(TaxSummaryReport $report)
    {
        $this->report = $report;
    }

    public function index()
    {
        $date_filter_by = [
            'invoice_date' => trans('fi.filter_by_invoice_date'),
            'payment_date' => trans('fi.filter_by_payment_date')
        ];
        return view('reports.options.tax_summary')->with('date_filter_by', $date_filter_by);
    }

    public function validateOptions(DateRangeRequest $request)
    {

    }

    public function html()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'tax_summary', 'title' => trans('fi.tax_summary'), 'id' => null]));

        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('exclude_unpaid_invoices'),
            request('date_filter_by')
        );

        return view('reports.output.tax_summary')
            ->with('results', $results);
    }

    public function pdf()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'tax_summary', 'title' => trans('fi.tax_summary'), 'id' => null]));

        $pdf = PDFFactory::create();

        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('exclude_unpaid_invoices'),
            request('date_filter_by')
        );

        try
        {
            $html = view('reports.output.tax_summary')
                ->with('results', $results)->render();
            $html = (str_replace(iframeThemeColor(), '', $html));
            $pdf->download($html, trans('fi.tax_summary') . '.pdf');
        }
        catch (Throwable $e)
        {
            Log::error($e->getMessage());
        }

    }

    public function csv()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'tax_summary', 'title' => trans('fi.tax_summary'), 'id' => null]));

        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('exclude_unpaid_invoices'),
            request('date_filter_by')
        );

        try
        {
            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=" . trans('fi.tax_summary') . '.csv',
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function () use ($results)
            {
                $file = fopen('php://output', 'w');

                fputcsv($file, [$results['from_date'], '', $results['to_date']]);
                fputcsv($file, ['', '', '']);
                fputcsv($file, [trans('fi.tax_rate'), trans('fi.taxable_amount'), trans('fi.taxes')]);
                foreach ($results['records'] as $taxRate => $result)
                {
                    fputcsv($file, [$taxRate, $result['taxable_amount'], $result['taxes']]);
                }
                fputcsv($file, ['', '', '']);
                fputcsv($file, ['', trans('fi.total'), $results['total']]);
                fputcsv($file, ['', trans('fi.paid'), $results['paid']]);
                fputcsv($file, ['', trans('fi.remaining'), $results['remaining']]);

                fclose($file);

            };

            return response()->streamDownload($callback, trans('fi.tax_summary') . '.csv', $headers);
        }
        catch (Throwable $e)
        {
            Log::error($e->getMessage());
        }

    }
}