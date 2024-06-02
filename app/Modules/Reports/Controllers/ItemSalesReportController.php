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
use FI\Modules\ItemLookups\Models\ItemCategory;
use FI\Modules\Mru\Events\MruLog;
use FI\Modules\Reports\Reports\ItemSalesReport;
use FI\Modules\Reports\Requests\DateRangeRequest;
use FI\Support\PDF\PDFFactory;
use Illuminate\Support\Facades\Log;
use Throwable;

class ItemSalesReportController extends Controller
{
    private $report;

    public function __construct(ItemSalesReport $report)
    {
        $this->report = $report;
    }

    public function index()
    {
        $sort_option = [
            'invoice_number' => trans('fi.filter_by_invoice_number'),
            'client_name'    => trans('fi.filter_by_client_name'),
            'invoice_date'   => trans('fi.filter_by_invoice_date'),
        ];
        return view('reports.options.item_sales')
            ->with('categories', ['' => trans('fi.all_categories')] + ItemCategory::getList())
            ->with('sort_option', $sort_option);
    }

    public function validateOptions(DateRangeRequest $request)
    {

    }

    public function html()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'item_sales', 'title' => trans('fi.item_sales'), 'id' => null]));

        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('category_id'),
            request('exclude_unpaid_invoices'),
            request('sort_option')
        );

        return view('reports.output.item_sales')
            ->with('results', $results);
    }

    public function pdf()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'item_sales', 'title' => trans('fi.item_sales'), 'id' => null]));

        $pdf = PDFFactory::create();

        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('category_id'),
            request('exclude_unpaid_invoices'),
            request('sort_option')
        );

        try
        {
            $html = view('reports.output.item_sales')
                ->with('results', $results)->render();
            $html = (str_replace(iframeThemeColor(), '', $html));
            $pdf->download($html, trans('fi.item_sales') . '.pdf');
        }
        catch (Throwable $e)
        {
            Log::error($e->getMessage());
        }
    }

    public function csv()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'item_sales', 'title' => trans('fi.item_sales'), 'id' => null]));

        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('category_id'),
            request('exclude_unpaid_invoices'),
            request('sort_option')
        );

        try
        {

            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=" . trans('fi.item_sales') . '.csv',
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $columns = [
                trans('fi.date'),
                trans('fi.invoice'),
                trans('fi.client'),
                trans('fi.price'),
                trans('fi.quantity'),
                trans('fi.subtotal'),
                trans('fi.discount'),
                trans('fi.tax'),
                trans('fi.total')
            ];

            $callback = function () use ($results, $columns)
            {
                if (count($results['records']) > 0)
                {
                    $file = fopen('php://output', 'w');

                    fputcsv($file, ['', '', $results['from_date'], '', $results['to_date'], '', '', '', '']);

                    foreach ($results['records'] as $key => $records)
                    {
                        fputcsv($file, ['', '', '', '', '', '', '', '', '']);
                        fputcsv($file, ['', '', '', '', $key, '', '', '', '']);
                        fputcsv($file, ['', '', '', '', '', '', '', '', '']);
                        fputcsv($file, $columns);

                        foreach ($records['items'] as $title => $data)
                        {
                            fputcsv($file, [
                                $data['date'],
                                $data['invoice_number'],
                                $data['client_name'],
                                $data['price'],
                                $data['quantity'],
                                $data['subtotal'],
                                $data['discount'],
                                $data['tax'],
                                $data['total']
                            ]);
                        }

                        fputcsv($file, ['', '', '', trans('fi.total'), $records['totals']['quantity'], $records['totals']['subtotal'], $records['totals']['discount'], $records['totals']['tax'], $records['totals']['total']]);
                    }

                    fputcsv($file, ['', '', '', '', '', '', '', trans('fi.grand_total'), $results['grand_total']]);

                    fclose($file);
                }
            };

            return response()->streamDownload($callback, trans('fi.item_sales') . '.csv', $headers);
        }
        catch (Throwable $e)
        {
            Log::error($e->getMessage());
        }

    }
}