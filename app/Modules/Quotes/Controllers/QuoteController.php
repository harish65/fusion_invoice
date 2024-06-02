<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Quotes\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Clients\Models\Client;
use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\Invoices\Requests\ColumnSettingStoreRequest;
use FI\Modules\Quotes\Models\Quote;
use FI\Modules\Quotes\Events\AddTransition;
use FI\Modules\Quotes\Events\QuoteToInvoiceTransition;
use FI\Modules\Quotes\Support\QuoteToInvoice;
use FI\Modules\Settings\Models\UserSetting;
use FI\Modules\Users\Models\User;
use FI\Support\CurrencyFormatter;
use FI\Support\DateFormatter;
use FI\Support\FileNames;
use FI\Support\PDF\PDFFactory;
use FI\Support\Statuses\QuoteStatuses;
use FI\Traits\ReturnUrl;
use File;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Response;
use Jurosh\PDFMerge\PDFMerger as PDFMerger;
use ZipArchive;

class QuoteController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();

        $sortable = ['quote_date' => 'desc', 'LENGTH(number)' => 'desc', 'number' => 'desc'];
        if (request()->has('s') && request()->has('o'))
        {
            Cookie::queue(Cookie::forever('quote_sort_column', request()->get('s')));
            Cookie::queue(Cookie::forever('quote_sort_order', request()->get('o')));
        }
        elseif (Cookie::get('quote_sort_column') && Cookie::get('quote_sort_order'))
        {
            request()->merge(['s' => Cookie::get('quote_sort_column'), 'o' => Cookie::get('quote_sort_order')]);
        }
        $quotesEloquentObject = Quote::select('quotes.*')
                                     ->join('clients', 'clients.id', '=', 'quotes.client_id')
                                     ->join('quote_amounts', 'quote_amounts.quote_id', '=', 'quotes.id')
                                     ->leftJoin('quotes_custom', 'quotes_custom.quote_id', '=', 'quotes.id')
                                     ->with(['client', 'activities', 'amount.quote.currency'])
                                     ->status(request('status'))
                                     ->keywords(request('search'))
                                     ->client(request('client'))
                                     ->dateRange(request('from_date'), request('to_date'))
                                     ->companyProfileId(request('company_profile'));

        $quotes              = self::getQuoteWithSortableAndPaginate($quotesEloquentObject, $sortable);
        $totalAndBalance     = self::getTotalAndBalancePageWise($quotes);
        $quoteColumnSettings = config('fi.quoteColumnSettings') == null ? User::quoteColumnSettings() : json_decode(config('fi.quoteColumnSettings'), true);
        $columnIndex         = countColumns(User::quoteColumnSettings(), $quoteColumnSettings);

        return view('quotes.index')
            ->with('quotes', $quotes)
            ->with('filterStatuses', ['' => trans('fi.all_statuses')] + QuoteStatuses::lists())
            ->with('bulkStatuses', QuoteStatuses::lists())
            ->with('companyProfiles', ['' => trans('fi.all_company_profiles')] + CompanyProfile::getList())
            ->with('clients', ['' => trans('fi.all_client')] + Client::getList())
            ->with('searchPlaceholder', trans('fi.search_quotes'))
            ->with('totalAndBalance', $totalAndBalance)
            ->with('quoteColumnSettings', $quoteColumnSettings)
            ->with('defaultQuoteSequenceColumnsData', User::quoteColumnSettings())
            ->with('columnIndex', $columnIndex);

    }

    public static function getTotalAndBalancePageWise($grandTotalAndBalance)
    {
        $formattedTotalAndBalance = $totalAndBalance = [];
        $index                    = 0;


        foreach ($grandTotalAndBalance as $value)
        {
            $totalAndBalance[$value->currency_code][] =
                [
                    'total'    => $value->amount->total,
                    'currency' => $value->currency,
                ];
        }

        foreach ($totalAndBalance as $key => $value)
        {
            $currencyColumn[$key]['total']           = array_column($value, 'total');
            $formattedTotalAndBalance[$key]['total'] = CurrencyFormatter::format(array_sum($currencyColumn[$key]['total']), $value[0]['currency']) . '(' . $key . ')';
            $formattedTotalAndBalance[$key]['index'] = $index++;

        }

        return $formattedTotalAndBalance;
    }

    public static function getQuoteWithSortableAndPaginate($quotesEloquentObject, $sortable)
    {
        return $quotesEloquentObject->sortable($sortable)->paginate(config('fi.resultsPerPage'));
    }

    public function delete($id)
    {
        $quote = Quote::find($id);
        event(new AddTransition($quote, 'deleted'));
        Quote::destroy($id);

        return redirect()->route('quotes.index')
                         ->with('alert', trans('fi.record_successfully_deleted'));
    }

    public function bulkDelete()
    {
        try
        {
            $quotes = Quote::whereIn('id', request('ids'))->get();
            foreach ($quotes as $quote)
            {
                event(new AddTransition($quote, 'deleted'));
            }
            Quote::destroy(request('ids'));

            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_deleted')], 200);
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('fi.quotes_delete_error')], 400);
        }
    }

    public function bulkStatus()
    {
        try
        {
            $ids = request('ids');
            foreach ($ids as $id)
            {
                $quote         = Quote::find($id);
                $quote->status = request('status');
                event(new AddTransition($quote, 'status_changed', $quote->getOriginal('status'), $quote->status));
                $quote->save();

                if ($quote->status == 'approved' && config('fi.convertQuoteWhenApproved'))
                {
                    $quoteToInvoice = new QuoteToInvoice();

                    $invoice = $quoteToInvoice->convert(
                        $quote,
                        date('Y-m-d'),
                        DateFormatter::incrementDateByDays(date('Y-m-d'), config('fi.invoicesDueAfter')),
                        config('fi.invoiceGroup')
                    );

                    event(new QuoteToInvoiceTransition($quote, $invoice));
                }
            }
            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_updated')], 200);
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('fi.status_change_error')], 400);
        }

    }

    public function bulkPdf()
    {
        $ids         = explode(',', request('ids'));
        $zipFileName = reset($ids) . '_' . end($ids) . '_quotes.zip';

        // Initializing PHP class
        $zip = new ZipArchive();

        $zip->open(storage_path('app/public/') . $zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $unlinkFiles = [];

        foreach ($ids as $id)
        {
            $quote = Quote::whereUserId(auth()->user()->id)->whereId($id)->first();
            if ($quote)
            {
                $pdf           = PDFFactory::create();
                $quotePdf      = FileNames::invoice($quote);
                $quotePdfPath  = base_path('assets/' . $quotePdf);
                $unlinkFiles[] = $quotePdfPath;
                $html          = darkModeForInvoiceAndQuoteTemplate($quote->html);
                $pdf->save($html, $quotePdfPath);
                $zip->addFile($quotePdfPath, $quotePdf);
            }
        }
        $zip->close();

        foreach ($unlinkFiles as $file)
        {
            if (file_exists($file))
            {
                unlink($file);
            }
        }

        return response()->download(storage_path('app/public/' . $zipFileName))->deleteFileAfterSend(true);
    }

    public function pdf($id)
    {
        $quote = Quote::find($id);

        $pdf = PDFFactory::create();

        $html = darkModeForInvoiceAndQuoteTemplate($quote->html);

        $pdf->download($html, FileNames::quote($quote));
    }

    public function savePdf($id)
    {
        $quote = Quote::find($id);

        $pdf = PDFFactory::create();

        $fileName = FileNames::invoice($quote);

        $html = darkModeForInvoiceAndQuoteTemplate($quote->html);

        $pdf->save($html, storage_path('app/public/') . $fileName);

        return route('invoices.print', [$fileName]);
    }

    public function printPdf($file)
    {
        return Response::make(file_get_contents(storage_path('app/public/' . $file)), 200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $file . '"',
            ]);
    }

    public function saveBulkPdf()
    {
        $ids       = explode(',', request('ids'));
        $fileNames = [];
        foreach ($ids as $id)
        {
            // This allows us to select invoices created by other users, but would fail trying to zip them.
            $quote    = Quote::whereId($id)->first();
            $pdf      = PDFFactory::create();
            $fileName = FileNames::invoice($quote);
            $html     = darkModeForInvoiceAndQuoteTemplate($quote->html);
            $pdf->save($html, storage_path('app/public/') . $fileName);
            $fileNames[] = $fileName;
        }

        return route('quotes.bulk.print', [implode(',', $fileNames)]);
    }

    public function printBulkPdf($files)
    {
        $files = explode(',', $files);
        $pdf   = new PDFMerger;
        foreach ($files as $file)
        {
            $pdf->addPDF(storage_path('app/public/' . $file), 'all');
        }

        $binaryContent = $pdf->merge('browser', storage_path('app/public/bulkPrint.pdf'));

        return Response::make($binaryContent, 200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="bulkPrint.pdf"',
            ]);
    }

    public function showFilterColumns()
    {
        return view('quotes._modal_filter_column')
            ->with('quoteColumnSettings', config('fi.quoteColumnSettings') == null ? User::quoteColumnSettings() : json_decode(config('fi.quoteColumnSettings'), true))
            ->with('defaultQuoteSequenceColumnsData', User::quoteColumnSettings());
    }

    public function storeQuotesListingColumnSettings(ColumnSettingStoreRequest $request)
    {
        $columns            = request('columns');
        $quote_list_columns = ['status', 'quote', 'date', 'expires', 'client', 'summary', 'total', 'invoiced'];
        if (request('columns'))
        {
            $json_data = refactorColumnSetting($quote_list_columns, $columns);

            $columnsUserSettingData = UserSetting::where('setting_key', 'quoteColumnSettings')
                                                 ->where('user_id', '=', auth()->user()->id)
                                                 ->get()->first();
            if ($columnsUserSettingData != '' && $columnsUserSettingData != null)
            {
                $columnsUserSettingData->setting_value = $json_data;
                $columnsUserSettingData->save();
            }
            else
            {
                $userSetting                = new UserSetting();
                $userSetting->user_id       = auth()->user()->id;
                $userSetting->setting_key   = 'quoteColumnSettings';
                $userSetting->setting_value = $json_data;
                $userSetting->save();
            }

        }

        return response()->json([], 200);

    }

    public function clientCreate()
    {
        return view('quotes._modal_client_create')
            ->with('clientName', request('client_name'))
            ->with('type', request('type'));
    }

    public function deleteModal()
    {
        try
        {
            return view('layouts._delete_modal_details')->with('url', request('action'))->with('returnURL', request('returnURL'))->with('modalName', request('modalName'))->with('isReload', request('isReload'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }

    public function deleteItemModal()
    {
        try
        {
            return view('layouts._delete_item_details_modal')
                ->with('itemId', request('itemId'))
                ->with('isReload', request('isReload'))
                ->with('modalName', request('modalName'))
                ->with('modelId', request('quoteId'))
                ->with('action', request('action'))
                ->with('refreshURL', request('refreshURL'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }


    public function bulkDeleteQuotesModal()
    {
        try
        {
            return view('layouts._bulk_delete_modal')
                ->with('url', request('action'))
                ->with('modalName', request('modalName'))
                ->with('data', json_encode(request('data')))
                ->with('returnURL', request('returnURL'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }

    public function bulkStatusChangeQuotesModal()
    {
        try
        {
            return view('quotes._bulk_status_change_quotes_modal')
                ->with('url', request('action'))
                ->with('modalName', request('modalName'))
                ->with('status', request('status'))
                ->with('data', json_encode(request('data')))
                ->with('returnURL', request('returnURL'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }
}