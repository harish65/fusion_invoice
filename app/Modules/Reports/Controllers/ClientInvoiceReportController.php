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
use FI\Modules\Reports\Reports\ClientInvoiceReport;
use FI\Modules\Reports\Requests\ClientInvoiceReportRequest;
use FI\Modules\Tags\Models\Tag;
use FI\Support\PDF\PDFFactory;
use FI\Support\Statuses\InvoiceStatuses;
use Illuminate\Support\Facades\Log;
use Throwable;

class ClientInvoiceReportController extends Controller
{
    private $report;

    public function __construct(ClientInvoiceReport $report)
    {
        $this->report = $report;
    }

    public function index()
    {

        return view('reports.options.client_invoice')
            ->with('tags', Tag::whereTagEntity('sales')->pluck('name', 'name'))
            ->with('filterStatuses', ['' => trans('fi.all_statuses')] + InvoiceStatuses::indexPageLists())
            ->with('clients', Client::getList());
    }

    public function validateOptions(ClientInvoiceReportRequest $request)
    {

    }

    public function html()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'client_invoice', 'title' => trans('fi.client_invoice'), 'id' => null]));

        $results = $this->report->getResults(
            request('client_id'),
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('invoice_status'),
            request('invoice_tags'),
            request('include_line_item_detail')
        );

        return view('reports.output.client_invoice')
            ->with('results', $results);
    }

    public function pdf()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'client_invoice', 'title' => trans('fi.client_invoice'), 'id' => null]));

        $pdf = PDFFactory::create();
        $pdf->setPaperOrientation('landscape');

        $results = $this->report->getResults(
            request('client_id'),
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('invoice_status'),
            request('invoice_tags'),
            request('include_line_item_detail')

        );

        try
        {
            $html = view('reports.output.client_invoice')
                ->with('results', $results)->render();
            $html = (str_replace(iframeThemeColor(), '', $html));
            $pdf->download($html, trans('fi.client_invoice') . '.pdf');
        }
        catch (Throwable $e)
        {
            Log::error($e->getMessage());
        }

    }

    public function csv()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'client_invoice', 'title' => trans('fi.client_invoice'), 'id' => null]));

        $results = $this->report->getResults(
            request('client_id'),
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('invoice_status'),
            request('invoice_tags'),
            request('include_line_item_detail')
        );

        try
        {

            $headers = [
                "Content-type"        => "text/csv; charset=UTF-8",
                "Content-Disposition" => "attachment; filename=" . trans('fi.client_invoice') . '.csv',
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0",

            ];

            $columns = [
                trans('fi.date'),
                trans('fi.invoice'),
                trans('fi.total'),
                trans('fi.paid'),
                trans('fi.balance'),
            ];

            if ($results['includeLineItemDetail'])
            {
                $callback = self::lineItemDetailDataPrepareForCSV($results);
            }
            else
            {
                unset($results['includeLineItemDetail']);

                $callback = function () use ($results, $columns) {
                    $file = fopen('php://output', 'w');

                    foreach ($results as $key => $records)
                    {
                        if (count($records['records']) > 0)
                        {
                            fputcsv($file, ['', '', '', '', '']);
                            fputcsv($file, ['', '', $key, '', '']);
                            fputcsv($file, ['', $records['from_date'], '', $records['to_date'], '']);
                            fputcsv($file, ['', '', '', '', '']);

                            foreach ($records['records'] as $currency => $data)
                            {
                                fputcsv($file, ['', '', $currency, '', '']);
                                fputcsv($file, $columns);

                                foreach ($data as $invoice_data)
                                {
                                    $date    = $invoice_data['formatted_invoice_date'];
                                    $invoice = $invoice_data['number'];
                                    $total   = $invoice_data['formatted_total'];
                                    $paid    = $invoice_data['formatted_paid'];
                                    $balance = $invoice_data['formatted_balance'];

                                    fputcsv($file, [$date, $invoice, $total, $paid, $balance]);
                                }
                                fputcsv($file, ['', '', $records['total'][$currency], $records['paid'][$currency], $records['balance'][$currency]]);
                            }
                        }
                    }

                    fclose($file);
                };
            }

            return response()->streamDownload($callback, trans('fi.client_invoice') . '.csv', $headers);
        }
        catch (Throwable $e)
        {
            Log::error($e->getMessage());
        }
    }

    public static function lineItemDetailDataPrepareForCSV($results)
    {
        unset($results['includeLineItemDetail']);

        $file = fopen('php://output', 'w');

        $lineItemDetailColumns = [
            trans('fi.product'),
            trans('fi.description'),
            trans('fi.price'),
            trans('fi.quantity'),
            trans('fi.subtotal'),
            trans('fi.discount'),
            trans('fi.tax'),
            trans('fi.total'),
        ];
        foreach ($results as $key => $result)
        {
            if (count($result['records']) > 0)
            {
                fputcsv($file, ['', '', '', '', '', '', '', '']);
                fputcsv($file, ['', '', '', $result['client_name'], '', '', '', '']);
                fputcsv($file, ['', '', $result['from_date'], '', '', $result['to_date'], '', '']);
                fputcsv($file, ['', '', '', '', '', '', '', '']);

                foreach ($result['records'] as $recordsKey => $records)
                {
                    foreach ($records as $recordKey => $record)
                    {
                        $invoiceNumber = $record['number'] . ' ' . $record['formatted_invoice_date'];
                        if (count($result['records']) > 1)
                        {
                            $invoiceNumber = $invoiceNumber . ' ( ' . $recordsKey . ' )';
                        }
                        fputcsv($file, ['', '', '', $invoiceNumber, '', '', '', '']);
                        fputcsv($file, $lineItemDetailColumns);

                        if (isset($record['items']) && $record['items'] > 0)
                        {
                            foreach ($record['items'] as $item)
                            {
                                $product     = $item['product'];
                                $description = $item['description'];
                                $price       = $item['price'];
                                $quantity    = $item['quantity'];
                                $subtotal    = $item['subtotal'];
                                $discount    = $item['discount'];
                                $tax         = $item['tax'];
                                $total       = $item['total'];
                                fputcsv($file, [$product, $description, $price, $quantity, $subtotal, $discount, $tax, $total]);
                            }
                            fputcsv($file, ['', '', trans('fi.total'), $record['items_totals']['quantity'], $record['items_totals']['subtotal'], $record['items_totals']['discount'], $record['items_totals']['tax'], $record['items_totals']['total']]);
                        }
                    }
                }
            }
        }
        fclose($file);
        return $file;
    }
}