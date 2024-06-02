<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\Commission\Controllers;

use Addons\Commission\Models\CommissionType;
use Addons\Commission\Models\InvoiceItemCommission;
use Addons\Commission\Reports\CommissionReport;
use FI\Http\Controllers\Controller;
use FI\Support\PDF\PDFFactory;
use FI\Traits\ReturnUrl;

class CommissionReportController extends Controller
{

    use ReturnUrl;


    public function __construct(CommissionReport $commissionReport)
    {
        $this->commissionReport = $commissionReport;
    }

    public function index()
    {
        return view('commission.reports.options.commission')
            ->with('users', InvoiceItemCommission::getUserDropDownList())
            ->with('types', CommissionType::getDropDownList())
            ->with('statuses', InvoiceItemCommission::getStatusList());
    }

    public function html()
    {
        $results = $this->commissionReport->getResults(
            request('from_date'),
            request('to_date'),
            request('user'),
            request('status'),
            request('type')
        );

        return view('commission.reports.output.commission')
            ->with('results', $results);
    }

    public function pdf()
    {
        $pdf = PDFFactory::create();

        $results = $this->commissionReport->getResults(
            request('from_date'),
            request('to_date'),
            request('user'),
            request('status'),
            request('commission_type')
        );

        $html = view('commission.reports.output.commission')
            ->with('results', $results)->render();

        $pdf->download($html, trans('Commission::lang.report') . '.pdf');
    }


    public function csv()
    {

        $results = $this->commissionReport->getResults(
            request('from_date'),
            request('to_date'),
            request('user'),
            request('status'),
            request('commission_type')
        );
        try
        {

            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=" . trans('Commission::lang.report') . '.csv',
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $columns = [
                trans('fi.invoice'),
                trans('fi.date'),
                trans('fi.client'),
                trans('Commission::lang.user'),
                trans('Commission::lang.commission_type'),
                trans('Commission::lang.product'),
                trans('Commission::lang.note'),
                trans('Commission::lang.status'),
                trans('fi.amount'),
            ];

            $callback = function () use ($results, $columns)
            {
                $file = fopen('php://output', 'w');
                foreach ($results['records'] as $commission_type => $result)
                {
                    fputcsv($file, ['', '', '', '', '', '', '', '', '']);
                    fputcsv($file, ['', '', '', trans('Commission::lang.' . $commission_type), '', '', '', '', '']);

                    foreach ($result as $user_id => $user_data)
                    {
                        fputcsv($file, $columns);

                        foreach ($user_data as $data)
                        {
                            $number        = $data['number'];
                            $date          = $data['date'];
                            $client        = $data['client'];
                            $user          = $data['user'];
                            $type          = $data['type'];
                            $product       = $data['product'];
                            $note          = $data['note'];
                            $status        = $data['status'];
                            $format_amount = $data['format_amount'];

                            fputcsv($file, [$number, $date, $client, $user, $type, $product, $note, $status, $format_amount]);
                        }
                        fputcsv($file, ['', '', '', '', '', '', '', '', '']);
                        fputcsv($file, ['', '', '', '', '', '', '', trans('Commission::lang.total'), $results['total'][$commission_type][$user_id]]);
                    }
                    fputcsv($file, ['', '', '', '', '', '', '', '', '']);
                    fputcsv($file, ['', '', '', '', '', '', '', trans('fi.grand_total'), $results['grand_total'][$commission_type]]);
                }

                fclose($file);
            };

            return response()->streamDownload($callback, trans('Commission::lang.report') . '.csv', $headers);
        }
        catch (Throwable $e)
        {
            Log::error($e->getMessage());
        }

    }

}
