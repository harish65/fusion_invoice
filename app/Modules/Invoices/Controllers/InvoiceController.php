<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Invoices\Controllers;

use Carbon\Carbon;
use FI\Http\Controllers\Controller;
use FI\Modules\Clients\Models\Client;
use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\Invoices\Events\AddTransition;
use FI\Modules\Invoices\Events\AllowEditingOfInvoicesInStatus;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Invoices\Requests\ColumnSettingStoreRequest;
use FI\Modules\Invoices\Support\InvoiceCalculate;
use FI\Modules\Settings\Models\UserSetting;
use FI\Modules\Tags\Models\Tag;
use FI\Modules\Users\Models\User;
use FI\Support\CurrencyFormatter;
use FI\Support\FileNames;
use FI\Support\PDF\PDFFactory;
use FI\Support\Statuses\InvoiceStatuses;
use FI\Traits\ReturnUrl;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Jurosh\PDFMerge\PDFMerger as PDFMerger;
use ZipArchive;

class InvoiceController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();
        $sortable = ['invoice_date' => 'desc', 'LENGTH(number)' => 'desc', 'number' => 'desc'];
        if (request()->has('s') && request()->has('o'))
        {
            Cookie::queue(Cookie::forever('invoice_sort_column', request()->get('s')));
            Cookie::queue(Cookie::forever('invoice_sort_order', request()->get('o')));
        }
        elseif (Cookie::get('invoice_sort_column') && Cookie::get('invoice_sort_order'))
        {
            request()->merge(['s' => Cookie::get('invoice_sort_column'), 'o' => Cookie::get('invoice_sort_order')]);
        }
        $tags             = json_decode(request('tags', '')) ?? [];
        $tagsMustMatchAll = request('tagsMustMatchAll', 0);

        $invoicesEloquentObject = Invoice::select('invoices.*',
            DB::raw("(SELECT COUNT(credit_memo.id) FROM " . DB::getTablePrefix() . "invoices as credit_memo inner join invoice_amounts on invoice_amounts.invoice_id = credit_memo.id
                        WHERE credit_memo.type = 'credit_memo' AND invoice_amounts.balance < 0 AND credit_memo.client_id = " . DB::getTablePrefix() . "invoices.client_id) as count_credit_memo"
            ),
            DB::raw("(SELECT COUNT(" . DB::getTablePrefix() . "payments.id) FROM " . DB::getTablePrefix() . "payments
                        WHERE " . DB::getTablePrefix() . "payments.client_id = " . DB::getTablePrefix() . "invoices.client_id AND " . DB::getTablePrefix() . "payments.remaining_balance > 0) as count_pre_payment"
            ),
            DB::raw("(SELECT COUNT(open_invoice.id) FROM " . DB::getTablePrefix() . "invoices as open_invoice
                        WHERE open_invoice.type = 'invoice' AND open_invoice.status IN ('sent','draft') AND open_invoice.client_id = " . DB::getTablePrefix() . "invoices.client_id) as count_sent_invoices"
            ))
            ->join('clients', 'clients.id', '=', 'invoices.client_id')
            ->join('invoice_amounts', 'invoice_amounts.invoice_id', '=', 'invoices.id')
            ->leftJoin('invoices_custom', 'invoices_custom.invoice_id', '=', 'invoices.id')
            ->with(['client', 'activities', 'amount.invoice.currency', 'custom'])
            ->status(request('status'))
            ->keywords(request('search'))
            ->client(request('client'))
            ->dateRange(request('from_date'), request('to_date'))
            ->companyProfileId(request('company_profile'))
            ->tags($tags, $tagsMustMatchAll);

        $invoices              = self::getInvoiceWithSortableAndPaginate($invoicesEloquentObject, $sortable);
        $totalAndBalance       = self::getTotalAndBalancePageWise($invoices);
        $invoiceColumnSettings = config('fi.invoiceColumnSettings') == null ? User::invoiceColumnSetting() : json_decode(config('fi.invoiceColumnSettings'), true);
        $columnIndex           = countColumns(User::invoiceColumnSetting(), $invoiceColumnSettings);

        return view('invoices.index')
            ->with('invoices', $invoices)
            ->with('filterStatuses', ['' => trans('fi.all_statuses')] + InvoiceStatuses::indexPageLists())
            ->with('bulkStatuses', collect(InvoiceStatuses::creditMemoLists()))
            ->with('companyProfiles', ['' => trans('fi.all_company_profiles')] + CompanyProfile::getList())
            ->with('searchPlaceholder', trans('fi.search_invoices'))
            ->with('tags', $tags)
            ->with('clients', ['' => trans('fi.all_client')] + Client::getList())
            ->with('tagsMustMatchAll', $tagsMustMatchAll)
            ->with('totalAndBalance', $totalAndBalance)
            ->with('invoiceColumnSettings', $invoiceColumnSettings)
            ->with('defaultSequenceColumnsData', User::invoiceColumnSetting())
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
                    'balance'  => $value->amount->balance,
                    'currency' => $value->currency,
                ];
        }

        foreach ($totalAndBalance as $key => $value)
        {
            $currencyColumn[$key]['total']             = array_column($value, 'total');
            $currencyColumn[$key]['balance']           = array_column($value, 'balance');
            $formattedTotalAndBalance[$key]['balance'] = CurrencyFormatter::format(array_sum($currencyColumn[$key]['balance']), $value[0]['currency']) . '(' . $key . ')';
            $formattedTotalAndBalance[$key]['total']   = CurrencyFormatter::format(array_sum($currencyColumn[$key]['total']), $value[0]['currency']) . '(' . $key . ')';
            $formattedTotalAndBalance[$key]['index']   = $index++;

        }

        return $formattedTotalAndBalance;
    }

    public static function getInvoiceWithSortableAndPaginate($invoicesEloquentObject, $sortable)
    {
        return $invoicesEloquentObject->sortable($sortable)->paginate(config('fi.resultsPerPage'));
    }

    public function showFilterTags()
    {
        $resultsPerPage   = 10;
        $selectedTags     = json_decode(request('tags', '[]'));
        $tagsMustMatchAll = request('tagsMustMatchAll', 0);

        $checkedTags = Tag::where('tag_entity', '=', 'sales')
            ->whereIn('id', $selectedTags)->get();
        $allTags     = Tag::where('tag_entity', '=', 'sales')
            ->whereNotIn('id', $selectedTags)
            ->paginate($resultsPerPage);

        $nextPageCount = $resultsPerPage;
        if (($allTags->total() - ($allTags->currentPage() * $resultsPerPage)) < $resultsPerPage)
        {
            $nextPageCount = $allTags->total() - ($allTags->currentPage() * $resultsPerPage);
        }

        $nextPageLink = '';
        if ($allTags->hasMorePages())
        {
            $params       = [
                'tags'             => json_encode($selectedTags),
                'tagsMustMatchAll' => $tagsMustMatchAll,
            ];
            $nextPageLink = $allTags->appends($params)->nextPageUrl();
        }

        if (request('firstLoad'))
        {
            return view('invoices._modal_filter_tags')
                ->with('selectedTags', $selectedTags)
                ->with('tagsMustMatchAll', $tagsMustMatchAll)
                ->with('nextPageLink', $nextPageLink)
                ->with('nextPageCount', $nextPageCount)
                ->with('checkedTags', $checkedTags)
                ->with('allTags', $allTags)
                ->with('hasNoTags', ((count($allTags) + count($checkedTags)) <= 0));
        }
        else
        {
            return response()->json([
                'html'          => view('invoices._filter_tags_list')
                    ->with('selectedTags', $selectedTags)
                    ->with('tagsMustMatchAll', $tagsMustMatchAll)
                    ->with('checkedTags', $checkedTags)
                    ->with('allTags', $allTags)->render(),
                'link'          => $nextPageLink,
                'nextPageCount' => $nextPageCount,
            ]);
        }
    }

    public function delete($id)
    {
        $invoice = Invoice::find($id);

        if ($invoice->payments->count() > 0)
        {
            return response()->json(['success' => false, 'message' => trans('fi.invoice_delete_error')], 400);
        }
        else if ($invoice->checkCommission())
        {
            return response()->json(['success' => false, 'message' => trans('fi.invoice_with_commission_delete_error')], 400);
        }
        else
        {
            if ($invoice->type == 'credit_memo')
            {
                event(new AddTransition($invoice, 'credit_memo_deleted'));
            }
            else
            {
                event(new AddTransition($invoice, 'deleted'));
            }
            $invoice->delete();
        }

    }

    public function bulkDelete()
    {
        try
        {
            $invoices = Invoice::whereIn('id', request('ids'))->get();
            foreach ($invoices as $invoice)
            {
                if ($invoice->payments->count() > 0)
                {
                    return response()->json(['success' => false, 'message' => trans('fi.invoice_delete_error')], 400);
                }
                else if ($invoice->checkCommission())
                {
                    return response()->json(['success' => false, 'message' => trans('fi.invoice_with_commission_delete_error')], 400);
                }
                else
                {
                    if ($invoice->type == 'credit_memo')
                    {
                        event(new AddTransition($invoice, 'credit_memo_deleted'));
                    }
                    else
                    {
                        event(new AddTransition($invoice, 'deleted'));
                    }
                    $invoice->delete();
                }
            }
            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_deleted')], 200);
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => $e->getMessage() . ' > ' . trans('fi.invoice_delete_error')], 400);
        }
    }

    public function bulkStatus()
    {
        try
        {
            $ids = request('ids');
            foreach ($ids as $id)
            {
                $invoice = Invoice::find($id);

                if (($invoice->amount->balance == 0 && $invoice->amount->total > 0 && $invoice->status != 'canceled') != true && $invoice->status !== 'applied')
                {
                    $invoiceCalculate = new InvoiceCalculate();
                    $invoice->status  = request('status');
                    $invoiceCalculate->calculate($invoice);
                    event(new AddTransition($invoice, 'status_changed', $invoice->getOriginal('status'), $invoice->status));
                    if ($invoice->type == 'invoice' && request('status') != 'applied')
                    {
                        $invoice->save();
                    }
                    elseif ($invoice->type == 'credit_memo' && request('status') != 'sent')
                    {
                        $invoice->save();
                    }
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
        $zipFileName = reset($ids) . '_' . end($ids) . '_invoices.zip';

        try
        {
            // Initializing PHP class
            $zip = new ZipArchive();

            $res = $zip->open(storage_path('app/public/') . $zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            if (!$res === true)
            {
                // write to log that creating zip file failed.  If app/pubic folder does not exist, this fails with $res = 5
                Log::error('There was an error creating the zip file. Zip return code: ' . $res);
            }
            else
            {
                $unlinkFiles = [];

                foreach ($ids as $id)
                {
                    // This allows us to select invoices created by other users, but would fail trying to zip them.
                    $invoice = Invoice::whereId($id)->first();
                    if ($invoice)
                    {
                        $pdf            = PDFFactory::create();
                        $invoicePdf     = FileNames::invoice($invoice);
                        $invoicePdfPath = base_path('assets/' . $invoicePdf);
                        $unlinkFiles[]  = $invoicePdfPath;
                        $html           = darkModeForInvoiceAndQuoteTemplate($invoice->html);
                        $pdf->save($html, $invoicePdfPath);
                        $zip->addFile($invoicePdfPath, $invoicePdf);
                    }
                    else
                    {
                        Log::error('PDF failed on Invoice id: ' . $id);
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
        }
        catch (\Exception $e)
        {
            Log::error('Zip operation error on open: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
        }
    }

    public function pdf($id)
    {
        $invoice = Invoice::find($id);

        $pdf = PDFFactory::create();

        $html = darkModeForInvoiceAndQuoteTemplate($invoice->html);

        $pdf->download($html, FileNames::invoice($invoice));
    }

    public function savePdf($id)
    {
        $invoice = Invoice::find($id);

        $pdf = PDFFactory::create();

        $fileName = FileNames::invoice($invoice);

        $html = darkModeForInvoiceAndQuoteTemplate($invoice->html);

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
            $invoice = Invoice::whereId($id)->first();

            $pdf = PDFFactory::create();

            $fileName = FileNames::invoice($invoice);

            $html = darkModeForInvoiceAndQuoteTemplate($invoice->html);

            $pdf->save($html, storage_path('app/public/') . $fileName);

            $fileNames[] = $fileName;
        }

        return route('invoices.bulk.print', [implode(',', $fileNames)]);
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
        return view('invoices._modal_filter_column')
            ->with('invoiceColumnSettings', config('fi.invoiceColumnSettings') == null ? User::invoiceColumnSetting() : json_decode(config('fi.invoiceColumnSettings'), true))
            ->with('defaultSequenceColumnsData', User::invoiceColumnSetting());
    }

    public function storeInvoiceListingColumnSettings(ColumnSettingStoreRequest $request)
    {
        $columns              = request('columns');
        $invoice_list_columns = ['status', 'invoice', 'recurring_id', 'date', 'due', 'client', 'summary', 'tags', 'total', 'balance'];
        if (request('columns'))
        {
            $json_data = refactorColumnSetting($invoice_list_columns, $columns);

            $columnsUserSettingData = UserSetting::where('setting_key', 'invoiceColumnSettings')
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
                $userSetting->setting_key   = 'invoiceColumnSettings';
                $userSetting->setting_value = $json_data;
                $userSetting->save();
            }

        }

        return response()->json([], 200);

    }

    public function printPdfAndMarkAsMailed($id)
    {
        $invoice              = Invoice::whereId($id)->first();
        $invoice->date_mailed = Carbon::now()->format('Y-m-d');
        $invoice->status      = 'sent';
        $invoice->save();

        $pdf      = PDFFactory::create();
        $fileName = FileNames::invoice($invoice);
        $html     = darkModeForInvoiceAndQuoteTemplate($invoice->html);
        $pdf->save($html, storage_path('app/public/') . $fileName);
        return route('invoices.print', [$fileName]);
    }

    public function mailed($id)
    {
        try
        {
            $invoice = Invoice::whereId($id)->first();

            $invoice->date_mailed = Carbon::now()->format('Y-m-d');

            $invoice->status = 'sent';

            $invoice->save();
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 401);
        }

        event(new AddTransition($invoice, 'mark_mail'));

        return response()->json(['success' => true, 'message' => trans('fi.invoice_mailed')], 200);
    }

    public function unMailed($id)
    {
        try
        {
            $invoice = Invoice::whereId($id)->first();

            $invoice->date_mailed = null;

            if ($invoice->virtual_status != null)
            {
                if (!(in_array('paid', $invoice->virtual_status) || in_array('overdue', $invoice->virtual_status) || in_array('emailed', $invoice->virtual_status)))
                {
                    $invoice->status = 'draft';
                }
            }
            else
            {
                $invoice->status = 'draft';
            }

            $invoice->save();
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 401);
        }

        event(new AddTransition($invoice, 'unmark_mail'));

        return response()->json(['success' => true, 'message' => trans('fi.invoice_unmark_mailed')], 200);
    }

    public function emptyInvoiceDelete($id)
    {
        $invoice = Invoice::whereId($id)->first();

        if ($invoice->items->first() == null)
        {
            $invoice->delete();
        }

    }

    public function showTimeLine()
    {
        $filterUsersData = [];
        if (auth()->user()->user_type == 'admin')
        {
            $users = User::select('id', 'name')->get()->toArray();
            foreach ($users as $user)
            {
                $filterUsersData[$user['id']] = $user['name'];
            }
        }

        $invoiceNumber = Invoice::whereId(request('invoice_id'))->first()->number;

        return view('invoices._modal_view_timeline')
            ->with('filterUsersData', $filterUsersData)
            ->with('invoiceId', request('invoice_id'))
            ->with('invoiceNumber', $invoiceNumber);
    }

    public function statusChangeToDraft($id)
    {
        try
        {
            $invoice = Invoice::whereId($id)->first();
            $status  = $invoice->status;

            if (($invoice->status == 'sent' && $invoice->payments->count() == 0) || $invoice->status == 'canceled')
            {
                $invoiceCalculate = new InvoiceCalculate();
                $invoice->status  = 'draft';
                $invoiceCalculate->calculate($invoice);
                $invoice->save();

                event(new AddTransition($invoice, 'status_changed', $status, $invoice->status));
            }
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 401);
        }

        return response()->json(['success' => true, 'message' => trans('fi.invoice_status_change_to_draft')], 200);
    }

    public function statusChangeToCancel($id)
    {
        try
        {
            $invoice = Invoice::whereId($id)->first();
            $status  = $invoice->status;

            if ($invoice->status != 'cancel' && $invoice->unPaid_status == true)
            {
                $invoiceCalculate = new InvoiceCalculate();
                $invoice->status  = 'canceled';
                $invoiceCalculate->calculate($invoice);
                $invoice->save();

                event(new AddTransition($invoice, 'status_changed', $status, $invoice->status));
            }
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 401);
        }

        return response()->json(['success' => true, 'message' => trans('fi.invoice_status_change_to_cancel')], 200);
    }

    public function clientCreate()
    {
        return view('invoices._modal_client_create')
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

    public function deleteInvoiceCommissionModal()
    {
        try
        {
            return view('invoices._delete_commission_item_details_modal')
                ->with('action', request('action'))
                ->with('returnURL', request('returnURL'))
                ->with('modalName', request('modalName'))
                ->with('index', request('index'))
                ->with('message', request('message'))
                ->with('isReload', request('isReload'));
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
                ->with('modelId', request('invoiceId'))
                ->with('action', request('action'))
                ->with('refreshURL', request('refreshURL'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }

    public function deletePaymentModal()
    {
        try
        {
            return view('invoices._delete_invoice_payment_modal')
                ->with('url', request('action'))
                ->with('modalName', request('modalName'))
                ->with('paymentInvoiceId', request('paymentInvoiceId'))
                ->with('isReload', request('isReload'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }

    public function bulkDeleteInvoicesModal()
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

    public function deleteCreditMemoApplicationModal()
    {
        try
        {
            return view('invoices._delete_credit_application_modal')
                ->with('action', request('action'))
                ->with('modalName', request('modalName'))
                ->with('isReload', request('isReload'))
                ->with('id', request('id'))
                ->with('message', request('message'))
                ->with('returnURL', request('returnURL'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }

    public function bulkStatusChangeInvoicesModal()
    {
        try
        {
            return view('invoices._bulk_status_change_invoices_modal')
                ->with('url', request('action'))
                ->with('modalName', request('modalName'))
                ->with('status', request('status'))
                ->with('data', json_encode(request('data')))
                ->with('message', request('message'))
                ->with('returnURL', request('returnURL'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }

    public function AllowEditingInvoicesInStatus()
    {
        if (request('status') && request('invoice_id'))
        {
            $invoice = Invoice::find(request('invoice_id'));

            event(new AllowEditingOfInvoicesInStatus($invoice, request('status')));
            return response()->json(['success' => true, 'message' => trans('fi.invoice_editable')], 200);
        }
        else
        {
            return response()->json(['success' => false, 'message' => trans('fi.somethings_wrongs')], 400);
        }

    }
}
