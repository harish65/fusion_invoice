<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\RecurringInvoices\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Clients\Models\Client;
use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\Invoices\Requests\ColumnSettingStoreRequest;
use FI\Modules\RecurringInvoices\Events\AddTransition;
use FI\Modules\RecurringInvoices\Models\RecurringInvoice;
use FI\Modules\Settings\Models\UserSetting;
use FI\Modules\Tags\Models\Tag;
use FI\Modules\Users\Models\User;
use FI\Support\CurrencyFormatter;
use FI\Support\Frequency;
use FI\Traits\ReturnUrl;
use Illuminate\Support\Facades\Cookie;

class RecurringInvoiceController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();

        $sortable = ['next_date' => 'desc', 'id' => 'desc'];
        if (request()->has('s') && request()->has('o'))
        {
            Cookie::queue(Cookie::forever('ri_sort_column', request()->get('s')));
            Cookie::queue(Cookie::forever('ri_sort_order', request()->get('o')));
        }
        elseif (Cookie::get('ri_sort_column') && Cookie::get('ri_sort_order'))
        {
            request()->merge(['s' => Cookie::get('ri_sort_column'), 'o' => Cookie::get('ri_sort_order')]);
        }
        $tags             = json_decode(request('tags', '')) ?? [];
        $tagsMustMatchAll = request('tagsMustMatchAll', 0);

        $recurringInvoicesEloquentObject = RecurringInvoice::select('recurring_invoices.*')
                                                           ->join('clients', 'clients.id', '=', 'recurring_invoices.client_id')
                                                           ->join('recurring_invoice_amounts', 'recurring_invoice_amounts.recurring_invoice_id', '=', 'recurring_invoices.id')
                                                           ->leftJoin('recurring_invoices_custom', 'recurring_invoices_custom.recurring_invoice_id', '=', 'recurring_invoices.id')
                                                           ->with(['client', 'activities', 'amount.recurringInvoice.currency'])
                                                           ->keywords(request('search'))
                                                           ->client(request('client'))
                                                           ->dateRange(request('from_date'), request('to_date'))
                                                           ->status(request('status'))
                                                           ->companyProfileId(request('company_profile'))
                                                           ->tags($tags, $tagsMustMatchAll);

        $recurringInvoices              = self::getRecurringInvoiceWithSortableAndPaginate($recurringInvoicesEloquentObject, $sortable);
        $totalAndBalance                = self::getTotalAndBalancePageWise($recurringInvoices);
        $recurringInvoiceColumnSettings = config('fi.recurringInvoiceColumnSettings') == null ? User::recurringInvoiceColumnSettings() : json_decode(config('fi.recurringInvoiceColumnSettings'), true);
        $columnIndex                    = countColumns(User::recurringInvoiceColumnSettings(), $recurringInvoiceColumnSettings);

        return view('recurring_invoices.index')
            ->with('recurringInvoices', $recurringInvoices)
            ->with('searchPlaceholder', trans('fi.search_recurring_invoices'))
            ->with('frequencies', Frequency::lists())
            ->with('statuses', ['all_statuses' => trans('fi.all_statuses'), 'active' => trans('fi.active'), 'inactive' => trans('fi.inactive')])
            ->with('companyProfiles', ['' => trans('fi.all_company_profiles')] + CompanyProfile::getList())
            ->with('clients', ['' => trans('fi.all_client')] + Client::getList())
            ->with('tags', $tags)
            ->with('tagsMustMatchAll', $tagsMustMatchAll)
            ->with('totalAndBalance', $totalAndBalance)
            ->with('recurringInvoiceColumnSettings', $recurringInvoiceColumnSettings)
            ->with('defaultRecurringInvoiceSequenceColumnsData', User::recurringInvoiceColumnSettings())
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

    public static function getRecurringInvoiceWithSortableAndPaginate($recurringInvoicesEloquentObject, $sortable)
    {
        return $recurringInvoicesEloquentObject->sortable($sortable)->paginate(config('fi.resultsPerPage'));
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
            return view('recurring_invoices._modal_filter_tags')
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
        $recurringInvoice = RecurringInvoice::find($id);
        event(new AddTransition($recurringInvoice, 'deleted'));
        $recurringInvoice->delete();
        return redirect()->route('recurringInvoices.index')
                         ->with('alert', trans('fi.record_successfully_deleted'));
    }

    public function showFilterColumns()
    {
        return view('recurring_invoices._modal_filter_column')
            ->with('recurringInvoiceColumnSettings', config('fi.recurringInvoiceColumnSettings') == null ? User::recurringInvoiceColumnSettings() : json_decode(config('fi.recurringInvoiceColumnSettings'), true))
            ->with('defaultRecurringInvoiceSequenceColumnsData', User::recurringInvoiceColumnSettings());
    }

    public function storeRecurringInvoiceListingColumnSettings(ColumnSettingStoreRequest $request)
    {
        $columns                        = request('columns');
        $recurring_invoice_list_columns = ['id', 'client', 'summary', 'next_date', 'stop_date', 'every', 'tags', 'total'];
        if (request('columns'))
        {
            $json_data = refactorColumnSetting($recurring_invoice_list_columns, $columns);

            $columnsUserSettingData = UserSetting::where('setting_key', 'recurringInvoiceColumnSettings')
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
                $userSetting->setting_key   = 'recurringInvoiceColumnSettings';
                $userSetting->setting_value = $json_data;
                $userSetting->save();
            }

        }

        return response()->json([], 200);

    }

    public function clientCreate()
    {
        return view('recurring_invoices._modal_client_create')
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
                ->with('modelId', request('recurringInvoiceId'))
                ->with('action', request('action'))
                ->with('refreshURL', request('refreshURL'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }

}