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
use FI\Modules\Clients\Models\Client;
use FI\Modules\Mru\Events\MruLog;
use FI\Modules\Reports\Reports\ClientStatementReport;
use FI\Modules\Reports\Requests\ClientStatementReportRequest;
use FI\Modules\Tags\Models\Tag;
use FI\Support\PDF\PDFFactory;
use FI\Support\Statuses\InvoiceStatuses;
use Illuminate\Support\Facades\Log;
use Throwable;

class ClientStatementReportController extends Controller
{
    private $report;

    public function __construct(ClientStatementReport $report)
    {
        $this->report = $report;
    }

    public function index()
    {
        return view('reports.options.client_statement')
            ->with('tags', Tag::whereTagEntity('sales')->pluck('name', 'name'))
            ->with('filterStatuses', ['' => trans('fi.all_statuses')] + InvoiceStatuses::indexPageLists())
            ->with('clients', ['' => trans('fi.all_client')] + Client::getList());
    }

    public function validateOptions(ClientStatementReportRequest $request)
    {

    }

    public function html()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'client_statement', 'title' => trans('fi.client_statement'), 'id' => null]));

        $results = $this->report->getResults(
            request('client_id'),
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('invoice_status'),
            request('invoice_tags')
        );

        return view('reports.output.client_statement')
            ->with('results', $results);
    }

    public function pdf()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'client_statement', 'title' => trans('fi.client_statement'), 'id' => null]));

        $pdf = PDFFactory::create();
        $pdf->setPaperOrientation('landscape');

        $results = $this->report->getResults(
            request('client_id'),
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('invoice_status'),
            request('invoice_tags')
        );

        try
        {
            $html = view('reports.output.client_statement')
                ->with('results', $results)->render();
            $html = (str_replace(iframeThemeColor(), '', $html));
            $pdf->download($html, trans('fi.client_statement') . '.pdf');
        }
        catch (Throwable $e)
        {
            Log::error($e->getMessage());
        }

    }

    public function csv()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'client_statement', 'title' => trans('fi.client_statement'), 'id' => null]));

        $results = $this->report->getResults(
            request('client_id'),
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('invoice_status'),
            request('invoice_tags')
        );

        try
        {

            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=" . trans('fi.client_statement') . '.csv',
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $columns = [
                trans('fi.date'),
                trans('fi.invoice'),
                trans('fi.summary'),
                trans('fi.subtotal'),
                trans('fi.discount'),
                trans('fi.tax'),
                trans('fi.total'),
                trans('fi.paid'),
                trans('fi.balance')
            ];

            $callback = function () use ($results, $columns)
            {
                $file = fopen('php://output', 'w');

                foreach ($results['records'] as $currency => $records)
                {
                    fputcsv($file, ['', '', '', $results['client_name'], '', '', '', '', '']);
                    fputcsv($file, ['', '', '', $results['from_date'], '', $results['to_date'], '', '', '']);
                    fputcsv($file, ['', '', '', '', $currency, '', '', '', '']);
                    fputcsv($file, ['', '', '', '', '', '', '', '', '']);
                    fputcsv($file, $columns);

                    foreach ($records as $data)
                    {
                        $date     = $data['formatted_invoice_date'];
                        $invoice  = $data['number'];
                        $summary  = $data['summary'];
                        $subtotal = $data['formatted_subtotal'];
                        $discount = $data['formatted_discount'];
                        $tax      = $data['formatted_tax'];
                        $total    = $data['formatted_total'];
                        $paid     = $data['formatted_paid'];
                        $balance  = $data['formatted_balance'];

                        fputcsv($file, [$date, $invoice, $summary, $subtotal, $discount, $tax, $total, $paid, $balance]);
                    }

                    fputcsv($file, ['', '', '', $results['subtotal'][$currency], $results['discount'][$currency], $results['tax'][$currency], $results['total'][$currency], $results['paid'][$currency], $results['balance'][$currency]]);
                }

                fclose($file);
            };

            return response()->streamDownload($callback, trans('fi.client_statement') . '.csv', $headers);
        }
        catch (Throwable $e)
        {
            Log::error($e->getMessage());
        }

    }
}