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
use FI\Modules\Expenses\Models\ExpenseCategory;
use FI\Modules\Expenses\Models\ExpenseVendor;
use FI\Modules\Mru\Events\MruLog;
use FI\Modules\Reports\Reports\ExpenseListReport;
use FI\Modules\Reports\Requests\DateRangeRequest;
use FI\Support\PDF\PDFFactory;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExpenseListReportController extends Controller
{
    private $report;

    public function __construct(ExpenseListReport $report)
    {
        $this->report = $report;
    }

    public function index()
    {
        $sort_option  = [
            'expense_date' => trans('fi.expense_date'),
            'category'     => trans('fi.category'),
            'vendor'       => trans('fi.vendor'),
            'amount'       => trans('fi.amount'),
        ];
        $group_option = [
            'none'     => trans('fi.none'),
            'category' => trans('fi.category'),
            'vendor'   => trans('fi.vendor'),
            'client'   => trans('fi.client'),
        ];
        return view('reports.options.expense_list')
            ->with('sort_option', $sort_option)
            ->with('group_option', $group_option)
            ->with('categories', ['' => trans('fi.all_categories')] + ExpenseCategory::getList())
            ->with('vendors', ['' => trans('fi.all_vendors')] + ExpenseVendor::getList());
    }

    public function validateOptions(DateRangeRequest $request)
    {

    }

    public function html()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'expense_list', 'title' => trans('fi.expense_list'), 'id' => null]));

        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('category_id'),
            request('vendor_id'),
            request('sort_option'),
            request('group_option')
        );

        return view('reports.output.expense_list')
            ->with('results', $results);
    }

    public function pdf()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'expense_list', 'title' => trans('fi.expense_list'), 'id' => null]));

        $pdf = PDFFactory::create();
        $pdf->setPaperOrientation('landscape');

        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('category_id'),
            request('vendor_id'),
            request('sort_option'),
            request('group_option')
        );

        try
        {
            $html = view('reports.output.expense_list')
                ->with('results', $results)->render();
            $html = (str_replace(iframeThemeColor(), '', $html));
            $pdf->download($html, trans('fi.expense_list') . '.pdf');
        }
        catch (Throwable $e)
        {
            Log::error($e->getMessage());
        }
    }

    public function csv()
    {
        event(new MruLog(['module' => 'reports', 'action' => 'expense_list', 'title' => trans('fi.expense_list'), 'id' => null]));

        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('category_id'),
            request('vendor_id'),
            request('sort_option'),
            request('group_option')
        );

        try
        {

            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=" . trans('fi.expense_list') . '.csv',
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $columns = [
                trans('fi.date'),
                trans('fi.client'),
                trans('fi.category'),
                trans('fi.vendor'),
                trans('fi.billed'),
                trans('fi.amount'),
                trans('fi.tax'),
                trans('fi.total')
            ];

            $callback = function () use ($results, $columns)
            {
                if (count($results['records']) > 0)
                {
                    $file = fopen('php://output', 'w');

                    foreach ($results['records'] as $key => $records)
                    {
                        fputcsv($file, ['', '', $results['from_date'], '', $results['to_date'], '', '', '']);
                        if ($key != 'none')
                        {
                            fputcsv($file, ['', '', '', $results['group_name'], '', '', '', '']);
                        }
                        fputcsv($file, ['', '', '', '', '', '', '', '']);
                        fputcsv($file, $columns);

                        foreach ($records['items'] as $data)
                        {
                            fputcsv($file, [
                                $data['date'],
                                $data['client'],
                                $data['category'],
                                $data['vendor'],
                                $data['billed'],
                                $data['amount'],
                                $data['tax'],
                                $data['total']
                            ]);
                        }

                        fputcsv($file, ['', '', '', '', '', '', trans('fi.subtotal'), $records['totals']['formatted_subtotal']]);
                    }

                    fputcsv($file, ['', '', '', '', '', '', trans('fi.total'), $results['formatted_total']]);

                    fclose($file);
                }
            };

            return response()->streamDownload($callback, trans('fi.expense_list') . '.csv', $headers);
        }
        catch (Throwable $e)
        {
            Log::error($e->getMessage());
        }

    }
}