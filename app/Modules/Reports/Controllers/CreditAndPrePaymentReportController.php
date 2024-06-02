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
use FI\Modules\Reports\Reports\CreditAndPrePaymentReport;
use FI\Modules\Reports\Requests\CreditAndPrePaymentReportRequest;
use FI\Support\PDF\PDFFactory;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreditAndPrePaymentReportController extends Controller
{
    private $report;

    public function __construct(CreditAndPrePaymentReport $report)
    {
        $this->report = $report;
    }

    public function index()
    {
        return view('reports.options.credit_and_prepayment')
            ->with('filterStatuses', ['' => trans('fi.all'), 'yes' => trans('fi.yes'), 'no' => trans('fi.no')])
            ->with('clients', Client::getList());
    }

    public function validateOptions(CreditAndPrePaymentReportRequest $request)
    {

    }

    public function html()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'credit_and_prepayment', 'title' => trans('fi.credit-memo-and-prepayments'), 'id' => null]));

        $credits = $this->report->getCreditResults(
            request('client_id'),
            request('from_date'),
            request('to_date'),
            request('status')
        );

        $pre_payments = $this->report->getPrePaymentResults(
            request('client_id'),
            request('from_date'),
            request('to_date'),
            request('status')
        );

        return view('reports.output.credit_and_prepayment')
            ->with('credits', $credits)
            ->with('pre_payments', $pre_payments);
    }

    public function pdf()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'credit_and_prepayment', 'title' => trans('fi.credit-memo-and-prepayments'), 'id' => null]));

        $pdf = PDFFactory::create();
        $pdf->setPaperOrientation('landscape');

        $credits = $this->report->getCreditResults(
            request('client_id'),
            request('from_date'),
            request('to_date'),
            request('status')
        );

        $pre_payments = $this->report->getPrePaymentResults(
            request('client_id'),
            request('from_date'),
            request('to_date'),
            request('status')
        );

        try
        {
            $html = view('reports.output.credit_and_prepayment')
                ->with('credits', $credits)
                ->with('pre_payments', $pre_payments)->render();
            $html = (str_replace(iframeThemeColor(), '', $html));
            $pdf->download($html, trans('fi.credit-memo-and-prepayments') . '.pdf');
        }
        catch (Throwable $e)
        {
            Log::error($e->getMessage());
        }

    }


    public function csv()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'credit_and_prepayment', 'title' => trans('fi.credit-memo-and-prepayments'), 'id' => null]));

        $credits = $this->report->getCreditResults(
            request('client_id'),
            request('from_date'),
            request('to_date'),
            request('status')
        );

        $pre_payments = $this->report->getPrePaymentResults(
            request('client_id'),
            request('from_date'),
            request('to_date'),
            request('status')
        );

        try
        {

            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=" . trans('fi.credit-memo-and-prepayments') . '.csv',
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $credit_columns = [
                trans('fi.date'),
                trans('fi.credit_memo'),
                trans('fi.total'),
                trans('fi.paid'),
                trans('fi.balance')
            ];

            $pre_payment_columns = [
                trans('fi.date'),
                '',
                trans('fi.total'),
                trans('fi.paid'),
                trans('fi.balance')
            ];

            $callback = function () use ($credits, $pre_payments, $credit_columns, $pre_payment_columns)
            {
                $file = fopen('php://output', 'w');

                foreach ($credits as $client => $credit)
                {
                    if (count($credit['records']) > 0)
                    {
                        fputcsv($file, ['', '', '', '', '']);
                        fputcsv($file, ['', '', $client, '', '']);
                        fputcsv($file, ['', $credit['from_date'], '', $credit['to_date'], '']);
                        fputcsv($file, ['', '', '', '', '']);

                        foreach ($credit['records'] as $key => $records)
                        {
                            fputcsv($file, ['', '', $key, '', '']);
                            fputcsv($file, $credit_columns);

                            foreach ($records as $record)
                            {
                                $date    = $record['formatted_invoice_date'];
                                $invoice = $record['number'];
                                $total   = $record['formatted_total'];
                                $paid    = $record['formatted_paid'];
                                $balance = $record['formatted_balance'];

                                fputcsv($file, [$date, $invoice, $total, $paid, $balance]);
                            }
                            fputcsv($file, ['', '', $credit['total'][$key], $credit['paid'][$key], $credit['balance'][$key]]);
                        }
                    }
                }

                foreach ($pre_payments as $client => $pre_payment)
                {
                    if (count($pre_payment['records']) > 0)
                    {
                        fputcsv($file, ['', '', '', '', '']);
                        fputcsv($file, ['', '', $client, '', '']);
                        fputcsv($file, ['', $pre_payment['from_date'], '', $pre_payment['to_date'], '']);
                        fputcsv($file, ['', '', '', '', '']);

                        foreach ($pre_payment['records'] as $key => $records)
                        {
                            fputcsv($file, ['', '', $key, '', '']);
                            fputcsv($file, $credit_columns);

                            foreach ($records as $record)
                            {
                                $date    = $record['formatted_invoice_date'];
                                $invoice = '';
                                $total   = $record['formatted_total'];
                                $paid    = $record['formatted_paid'];
                                $balance = $record['formatted_balance'];

                                fputcsv($file, [$date, $invoice, $total, $paid, $balance]);
                            }
                            fputcsv($file, ['', '', $pre_payment['total'][$key], $pre_payment['paid'][$key], $pre_payment['balance'][$key]]);
                        }
                    }
                }

                fclose($file);
            };

            return response()->streamDownload($callback, trans('fi.credit-memo-and-prepayments') . '.csv', $headers);
        }
        catch (Throwable $e)
        {
            Log::error($e->getMessage());
        }

    }
}