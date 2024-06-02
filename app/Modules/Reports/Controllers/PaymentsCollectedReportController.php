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
use FI\Modules\Reports\Reports\PaymentsCollectedReport;
use FI\Modules\Reports\Requests\DateRangeRequest;
use FI\Modules\Tags\Models\Tag;
use FI\Support\PDF\PDFFactory;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentsCollectedReportController extends Controller
{
    private $report;

    public function __construct(PaymentsCollectedReport $report)
    {
        $this->report = $report;
    }

    public function index()
    {
        return view('reports.options.payments_collected')
            ->with('tags', Tag::whereTagEntity('sales')->pluck('name', 'name'));
    }

    public function validateOptions(DateRangeRequest $request)
    {

    }

    public function html()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'payments_collected', 'title' => trans('fi.payments_collected'), 'id' => null]));

        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('prepayments'),
            request('currency_format'),
            request('invoice_tags')
        );

        return view('reports.output.payments_collected')
            ->with('results', $results);
    }

    public function pdf()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'payments_collected', 'title' => trans('fi.payments_collected'), 'id' => null]));

        $pdf = PDFFactory::create();
        $pdf->setPaperOrientation('landscape');

        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('prepayments'),
            request('currency_format'),
            request('invoice_tags')
        );

        try
        {
            $html = view('reports.output.payments_collected')
                ->with('results', $results)->render();
            $html = (str_replace(iframeThemeColor(), '', $html));
            $pdf->download($html, trans('fi.payments_collected') . '.pdf');
        }
        catch (Throwable $e)
        {
            Log::error($e->getMessage());
        }
    }

    public function csv()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'payments_collected', 'title' => trans('fi.payments_collected'), 'id' => null]));

        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('prepayments'),
            request('currency_format'),
            request('invoice_tags')
        );

        try
        {

            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=" . trans('fi.payments_collected') . '.csv',
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $columns = [
                trans('fi.date'),
                trans('fi.invoice'),
                trans('fi.client'),
                trans('fi.payment_method'),
                trans('fi.note'),
                trans('fi.amount')
            ];

            $callback = function () use ($results, $columns)
            {
                if (count($results['records']) > 0)
                {
                    $file = fopen('php://output', 'w');

                    fputcsv($file, ['',  $results['from_date'], '', $results['to_date'], '', '']);

                    foreach ($results['records'] as $key => $records)
                    {
                        fputcsv($file, ['', '', '', '', '', '']);
                        fputcsv($file, ['', '', $key, '', '', '']);
                        fputcsv($file, ['', '', '', '', '', '']);
                        fputcsv($file, $columns);

                        foreach ($records['payments'] as $title => $data)
                        {
                            $amount = $results['currency_format'] == 'fi.base_currency' ? $data['amount'] : $data['amount_with_currency'];
                            fputcsv($file, [
                                $data['date'],
                                $data['invoice_number'],
                                $data['client_name'],
                                $data['payment_method'],
                                $data['note'],
                                $amount
                            ]);
                        }

                        fputcsv($file, ['', '', '', '', trans('fi.total'), $records['totals']['amount']]);
                    }

                    fputcsv($file, ['', '', '', '',trans('fi.grand_total'), $results['grand_total']]);

                    fclose($file);
                }
            };

            return response()->streamDownload($callback, trans('fi.payments_collected') . '.csv', $headers);
        }
        catch (Throwable $e)
        {
            Log::error($e->getMessage());
        }
    }
}