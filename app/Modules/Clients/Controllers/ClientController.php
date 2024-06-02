<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Clients\Controllers;

use Carbon\Carbon;
use FI\Http\Controllers\Controller;
use FI\Modules\Addons\Models\Addon;
use FI\Modules\Clients\Models\Client;
use FI\Modules\Clients\Requests\ClientOnTheFlyStoreRequest;
use FI\Modules\Clients\Requests\ClientStoreRequest;
use FI\Modules\Clients\Requests\ClientUpdateRequest;
use FI\Modules\Clients\Requests\ColumnSettingStoreRequest;
use FI\Modules\Countries\Models\Country;
use FI\Modules\CustomFields\Models\ClientCustom;
use FI\Modules\CustomFields\Support\CustomFieldsParser;
use FI\Modules\CustomFields\Support\CustomFieldsTransformer;
use FI\Modules\Mru\Events\MruLog;
use FI\Modules\Payments\Models\Payment;
use FI\Modules\Tags\Models\Tag;
use FI\Modules\TaskList\Models\Task;
use FI\Modules\Transitions\Models\Transitions;
use FI\Modules\Users\Models\User;
use FI\Support\Frequency;
use FI\Traits\ReturnUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cookie;

class ClientController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();

        $sortable = ['name' => 'asc'];
        if (request()->has('s') && request()->has('o'))
        {
            Cookie::queue(Cookie::forever('client_sort_column', request()->get('s')));
            Cookie::queue(Cookie::forever('client_sort_order', request()->get('o')));
        }
        elseif (Cookie::get('client_sort_column') && Cookie::get('client_sort_order'))
        {
            request()->merge(['s' => Cookie::get('client_sort_column'), 'o' => Cookie::get('client_sort_order')]);
        }
        $tags             = json_decode(request('tags', '')) ?? [];
        $tagsMustMatchAll = request('tagsMustMatchAll', 0);

        $clients = Client::getSelect()
            ->leftJoin('clients_custom', 'clients_custom.client_id', '=', 'clients.id')
            ->with(['currency'])
            ->sortable($sortable)
            ->status(request('status'))
            ->type(request('type'))
            ->keywords(request('search'))
            ->tags($tags, $tagsMustMatchAll)
            ->paginate(config('fi.resultsPerPage'));
        return view('clients.index')
            ->with('clients', $clients)
            ->with('searchPlaceholder', trans('fi.search_clients'))
            ->with('types', ['' => trans('fi.show_all_types')] + Client::getTypesList())
            ->with('statuses', ['' => trans('fi.show_all_statuses')] + Client::getStatusList())
            ->with('tags', $tags)
            ->with('tagsMustMatchAll', $tagsMustMatchAll);
    }

    public function create()
    {
        return view('clients.form')
            ->with('selectedTab', request('tab', 'general'))
            ->with('editMode', false)
            ->with('parentClient', true)
            ->with('customFields', CustomFieldsParser::getFields('clients'))
            ->with('tags', Tag::whereTagEntity('client')->pluck('name', 'name'))
            ->with('selectedTags', [])
            ->with('leadSourceTags', ['' => trans('fi.select_lead_source_tag')] + Tag::whereTagEntity('client_lead_source')->pluck('name', 'id')->toArray())
            ->with('selectedLeadSourceTags', null)
            ->with('countries', Country::getAll())
            ->with('clientTitle', ['' => trans('fi.select_client_title')] + Client::getClientTitle())
            ->with('parentClients', ['' => trans('fi.select_parent_client')] + Client::getParentClients())
            ->with('invoicesPaidBy', Client::getInvoicesPaidByClients());
    }

    public function duplicateName()
    {
        $data                   = request('data');
        $data['saveForceFully'] = 1;

        $client = Client::query();

        if (isset($data['client_email']) && $data['client_email'] != null)
        {
            $client->orWhere('email', 'like', $data['client_email'] . '%');
        }
        if (isset($data['name']) && $data['name'] != null)
        {
            $names = explode(" ", $data['name']);
            $client->orWhere('name', 'LIKE', $names[0] . ' %');
            $client->orWhere('name', $names[0]);
        }
        if (isset($data['phone']) && $data['phone'] != null)
        {
            $client->orWhere('phone', 'like', $data['phone'] . '%');
        }
        $client = $client->get();

        return view('clients._modal_duplicate_client_name')->with('clients', $client)->with('requestData', $data)->with('duplicate', true);
    }

    public function store(ClientStoreRequest $request)
    {
        $client = Client::query();

        if (request('client_email') != null)
        {
            $client->orWhere('email', 'like', request('client_email') . '%');
        }

        if (request('name') != null)
        {
            $names = explode(" ", request('name'));
            $client->orWhere('name', 'LIKE', $names[0] . ' %');
            $client->orWhere('name', $names[0]);
        }

        if (request('phone') != null)
        {
            $client->orWhere('phone', 'like', request('phone') . '%');
        }
        $client = $client->get();

        if (!$client->isEmpty() == true)
        {
            return response(['mess' => true, 'duplicate' => true, 'duplicateData' => request()->all()]);
        }

        $input                           = $request->except('custom', 'tags', 'cname', 'cemail', 'password', 'password_confirmation', 'allow_client_center_login');
        $input['user_id']                = auth()->user()->id;
        $input['allow_child_accounts']   = isset($input['allow_child_accounts']) ? $input['allow_child_accounts'] : 0;
        $input['third_party_bill_payer'] = isset($input['third_party_bill_payer']) ? $input['third_party_bill_payer'] : 0;
        $input['lead_source_tag_id']     = ($request->lead_source_tag_id == '') ? null : $input['lead_source_tag_id'];
        $client                          = Client::create($input);

        // Save the custom fields.
        $customFieldData = CustomFieldsTransformer::transform(request('custom', []), 'clients', $client);
        $client->custom->update($customFieldData);

        $manageTags = manageTags($client, 'client_tag_updated', 'client_tag_deleted', 'Clients');

        $tags    = isset($manageTags) ? $manageTags : $request->input('tags', []);
        $tag_ids = [];

        foreach ($tags as $tag)
        {
            $tag = Tag::firstOrNew(['name' => $tag, 'tag_entity' => 'client'])->fill(['name' => $tag, 'tag_entity' => 'client']);

            $tag->save();

            $tag_ids[] = $tag->id;
        }

        foreach ($tag_ids as $tag_id)
        {
            $client->tags()->create(['client_id' => $client->id, 'tag_id' => $tag_id]);
        }

        // If client center login allowed and password inserted then need to create user with client
        if ($request->get('allow_client_center_login') == 1)
        {
            $password = $request->get('password');

            $user = new User(['email' => $client->email, 'name' => $client->name, 'user_type' => 'client']);

            $user->password  = $password;
            $user->client_id = $client->id;

            $user->save();

        }

        return response(['clientId' => $client->id, 'alertSuccess' => trans('fi.record_successfully_created')]);
    }

    public function duplicateStore(Request $request)
    {

        $input                           = $request->except('custom', 'tags', 'cname', 'cemail', 'password', 'password_confirmation', 'allow_client_center_login');
        $input['user_id']                = auth()->user()->id;
        $input['allow_child_accounts']   = isset($input['allow_child_accounts']) ? $input['allow_child_accounts'] : 0;
        $input['third_party_bill_payer'] = isset($input['third_party_bill_payer']) ? $input['third_party_bill_payer'] : 0;
        $input['lead_source_tag_id']     = ($request->lead_source_tag_id == '') ? null : $input['lead_source_tag_id'];
        $input['email']                  = $request['client_email'];
        $client                          = Client::create($input);

        // Save the custom fields.
        $customFieldData = CustomFieldsTransformer::transform(request('custom', []), 'clients', $client);
        $client->custom->update($customFieldData);

        $manageTags = manageTags($client, 'client_tag_updated', 'client_tag_deleted', 'Clients');

        $tags    = isset($manageTags) ? $manageTags : $request->input('tags', []);
        $tag_ids = [];

        foreach ($tags as $tag)
        {
            $tag = Tag::firstOrNew(['name' => $tag, 'tag_entity' => 'client'])->fill(['name' => $tag, 'tag_entity' => 'client']);

            $tag->save();

            $tag_ids[] = $tag->id;
        }

        foreach ($tag_ids as $tag_id)
        {
            $client->tags()->create(['client_id' => $client->id, 'tag_id' => $tag_id]);
        }

        // If client center login allowed and password inserted then need to create user with client
        if ($request->get('allow_client_center_login') == 1)
        {
            $password = $request->get('password');

            $user = new User(['email' => $client->email, 'name' => $client->name, 'user_type' => 'client']);

            $user->password  = $password;
            $user->client_id = $client->id;

            $user->save();

        }

        return response(['message' => trans('fi.record_successfully_created'), 'flag' => true, 'clientId' => $client->id]);

    }

    public function storeOnTheFly(ClientOnTheFlyStoreRequest $request)
    {
        $input            = $request->only('name');
        $input['user_id'] = auth()->user()->id;
        $input['type']    = 'customer';
        $client           = Client::create($input);

        // Save the custom fields.
        $customFieldData = CustomFieldsTransformer::transform(request('custom', []), 'clients', $client);
        $client->custom->update($customFieldData);

        return response()->json(['success' => true, 'client_id' => $client->id], 200);
    }

    public function show($clientId)
    {

        $this->setReturnUrl();
        try
        {
            $client = Client::with(['tags.tag', 'contacts'])->find($clientId);

            event(new MruLog(['module' => 'clients', 'action' => 'view', 'id' => $clientId, 'title' => $client->name]));
        }
        catch (\Exception $e)
        {
            return redirect(route('clients.index'))->with('error', trans('fi.no_client_found'));
        }
        $invoices = $client->invoices()
            ->select('invoices.*',
                DB::raw("(SELECT COUNT(credit_memo.id) FROM " . DB::getTablePrefix() . "invoices as credit_memo inner join invoice_amounts on invoice_amounts.invoice_id = credit_memo.id
                        WHERE credit_memo.type = 'credit_memo' AND invoice_amounts.balance < 0 AND credit_memo.client_id = " . DB::getTablePrefix() . "invoices.client_id) as count_credit_memo"
                ),
                DB::raw("(SELECT COUNT(" . DB::getTablePrefix() . "payments.id) FROM " . DB::getTablePrefix() . "payments
                        WHERE " . DB::getTablePrefix() . "payments.client_id = " . DB::getTablePrefix() . "invoices.client_id AND " . DB::getTablePrefix() . "payments.remaining_balance > 0) as count_pre_payment"
                ),
                DB::raw("(SELECT COUNT(open_invoice.id) FROM " . DB::getTablePrefix() . "invoices as open_invoice
                        WHERE open_invoice.type = 'invoice' AND open_invoice.status IN ('sent','draft') AND open_invoice.client_id = " . DB::getTablePrefix() . "invoices.client_id) as count_sent_invoices"
                )
            )
            ->with(['client', 'activities', 'amount.invoice.currency'])
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->take(config('fi.resultsPerPage'))->get();

        $quotes = $client->quotes()
            ->with(['client', 'activities', 'amount.quote.currency', 'invoice'])
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->take(config('fi.resultsPerPage'))->get();

        $recurringInvoices = $client->recurringInvoices()
            ->with(['client', 'amount.recurringInvoice.currency'])
            ->orderBy('next_date', 'desc')
            ->orderBy('id', 'desc')
            ->take(config('fi.resultsPerPage'))->get();

        $filterUsers = [];
        if (auth()->user()->user_type == 'admin')
        {
            $users = User::select('id', 'name')->get()->toArray();
            foreach ($users as $user)
            {
                $filterUsers[$user['id']] = $user['name'];
            }
        }

        return view('clients.view')
            ->with('client', $client)
            ->with('invoicePaymentSummary', $client->currencyWiseSummary())
            ->with('selectedTab', request('tab', 'general'))
            ->with('invoices', $invoices)
            ->with('quotes', $quotes)
            ->with('payments', Payment::clientId($clientId)->whereNull('credit_memo_id')->orderBy('created_at', 'desc')->get())
            ->with('recurringInvoices', $recurringInvoices)
            ->with('customFields', CustomFieldsParser::getFields('clients'))
            ->with('frequencies', Frequency::lists())
            ->with('childClients', Client::getChildClients($clientId))
            ->with('thirdPartyBillPayers', Client::getThirdPartyBillPayers($clientId))
            ->with('relatedAccounts', ['count' => (count(Client::getChildClients($clientId)) + count(Client::getThirdPartyBillPayers($clientId))), 'active' => ((count(Client::getThirdPartyBillPayers($clientId)) > 0) ? ((count(Client::getChildClients($clientId)) > 0) ? 'childClient' : 'thirdBP') : 'childClient')])
            ->with('tags', [])
            ->with('modules', Transitions::getModulesList())
            ->with('filterUsers', $filterUsers)
            ->with('tagsMustMatchAll', 0)
            ->with('tasks', Task::whereClientId($clientId)->get())
            ->with('containerAddonStatus', Addon::getContainersAddonStatus())
            ->with('typeLabels', ['lead' => 'badge-warning', 'prospect' => 'badge-danger', 'customer' => 'badge-success', 'affiliate' => 'badge-info', 'other' => 'badge-secondary'])
            ->with('invoiceColumnSettings', config('fi.invoiceColumnSettings') == null ? User::invoiceColumnSetting() : json_decode(config('fi.invoiceColumnSettings'), true))
            ->with('defaultSequenceColumnsData', User::invoiceColumnSetting())
            ->with('recurringInvoiceColumnSettings', config('fi.recurringInvoiceColumnSettings') == null ? User::recurringInvoiceColumnSettings() : json_decode(config('fi.recurringInvoiceColumnSettings'), true))
            ->with('defaultRecurringInvoiceSequenceColumnsData', User::recurringInvoiceColumnSettings())
            ->with('quoteColumnSettings', config('fi.quoteColumnSettings') == null ? User::quoteColumnSettings() : json_decode(config('fi.quoteColumnSettings'), true))
            ->with('defaultQuoteSequenceColumnsData', User::quoteColumnSettings());
    }

    public function invoiceSummary($id, $currency_code)
    {
        $client = Client::find($id);
        return view('clients.summary')
            ->with('currency', $currency_code)
            ->with('invoicePaymentSummary', $client->currencyWiseSummary());
    }

    public function edit($clientId)
    {

        $client = Client::getSelect()->with(['custom', 'tags.tag'])->find($clientId);

        event(new MruLog(['module' => 'clients', 'action' => 'view', 'id' => $clientId, 'title' => $client->name]));

        $selectedTags = [];

        foreach ($client->tags as $tagDetail)
        {
            $selectedTags[] = $tagDetail->tag->name;
        }

        $invoicesPaidByClients = Client::getInvoicesPaidByClients();
        if ($invoicesPaidByClients != null)
        {
            if (array_key_exists($clientId, $invoicesPaidByClients) == true)
            {
                unset($invoicesPaidByClients[$clientId]);
            }
        }

        return view('clients.form')
            ->with('editMode', true)
            ->with('parentClient', true)
            ->with('client', $client)
            ->with('selectedTab', request('tab', 'general'))
            ->with('customFields', CustomFieldsParser::getFields('clients'))
            ->with('tags', Tag::whereTagEntity('client')->pluck('name', 'name'))
            ->with('selectedTags', $selectedTags)
            ->with('leadSourceTags', ['' => trans('fi.select_lead_source_tag')] + Tag::whereTagEntity('client_lead_source')->pluck('name', 'id')->toArray())
            ->with('selectedLeadSourceTags', $client->lead_source_tag_id)
            ->with('typeLabels', ['lead' => 'badge-warning', 'prospect' => 'badge-danger', 'customer' => 'badge-success', 'affiliate' => 'badge-info', 'other' => 'badge-secondary'])
            ->with('parentClients', ['' => trans('fi.select_parent_client')] + Client::getParentClients($clientId))
            ->with('invoicesPaidBy', $invoicesPaidByClients)
            ->with('countries', Country::getAll())
            ->with('returnUrl', $this->getReturnUrl());

    }

    public function update(ClientUpdateRequest $request, $id)
    {
        /** @var Client $client */
        $client = Client::find($id);
        $client->fill($request->except('custom', 'tags', 'cname', 'cemail', 'password', 'password_confirmation', 'allow_client_center_login', 'tab'));
        $client->lead_source_tag_id = ($request->lead_source_tag_id == '') ? null : $request->lead_source_tag_id;
        $client->save();

        // Save the custom fields.
        $customFieldData = CustomFieldsTransformer::transform(request('custom', []), 'clients', $client);
        $client->custom->update($customFieldData);

        $manageTags = manageTags($client, 'client_tag_updated', 'client_tag_deleted', 'Clients');

        $tags    = isset($manageTags) ? $manageTags : $request->input('tags', []);
        $tag_ids = [];

        foreach ($tags as $tag)
        {
            $tag = Tag::firstOrNew(['name' => $tag, 'tag_entity' => 'client'])->fill(['name' => $tag, 'tag_entity' => 'client']);

            $tag->save();

            $tag_ids[] = $tag->id;
        }

        foreach ($tag_ids as $tag_id)
        {
            $client->tags()->insert(['client_id' => $client->id, 'tag_id' => $tag_id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        }

        // If client center login allowed and password inserted then we have to upsert entry on user table
        $allowClientCenterLogin = $request->get('allow_client_center_login', '');
        $password               = $request->get('password');
        if ($allowClientCenterLogin == 1)
        {
            if (isset($client->user))
            {
                $user = User::find($client->user->id);
                $user->fill(['email' => $client->email, 'name' => $client->name]);
                if ($password)
                {
                    $user->password = $password;
                }
                $user->save();
            }
            else
            {
                $user            = new User(['email' => $client->email, 'name' => $client->name, 'user_type' => 'client']);
                $user->password  = $password;
                $user->client_id = $client->id;
                $user->save();

            }
        }

        if ($allowClientCenterLogin == '' && isset($client->user))
        {
            User::find($client->user->id)->delete();
        }

        return redirect()->route('clients.show', [$id, 'tab' => request('tab', 'general')])
            ->with('selectedTab', request('tab', 'general'))
            ->with('alertSuccess', trans('fi.record_successfully_updated'));
    }

    public function delete($clientId)
    {
        $client = Client::find($clientId);

        $module   = [];
        $module[] = $client->payments->count() > 0 ? trans('fi.payments') : '';
        $module[] = $client->notes->count() > 0 ? trans('fi.notes') : '';
        $module[] = $client->expenses->count() > 0 ? trans('fi.expenses') : '';
        $module[] = $client->tasks->count() > 0 ? trans('fi.tasks') : '';
        $module[] = $client->quotes->count() > 0 ? trans('fi.quotes') : '';
        $module[] = $client->recurringInvoices->count() > 0 ? trans('fi.recurring_invoices') : '';
        $module[] = $client->invoices->count() > 0 ? trans('fi.invoices') : '';
        $module[] = !empty($client->parent_client_id) ? trans('fi.parent_account') : '';
        $module[] = !empty($client->invoices_paid_by) ? trans('fi.third_party_bill_payer') : '';
        $module   = array_filter($module);

        if (count($module) > 0)
        {
            $error = trans('fi.client_related_record_exist', ['modules' => implode(', ', $module)]);
            return response()->json([
                'success' => false,
                'errors'  => ['messages' => [$error]],
            ], 400);
        }

        $client->delete();

        return response()->json([
            'success' => true,
            'message' => trans('fi.record_successfully_deleted'),
        ], 200);
    }

    public function ajaxModalEdit()
    {
        $client       = Client::getSelect()->with(['custom', 'tags.tag'])->find(request('client_id'));
        $selectedTags = [];

        foreach ($client->tags as $tagDetail)
        {
            $selectedTags[] = $tagDetail->tag->name;
        }

        $invoicesPaidByClients = Client::getInvoicesPaidByClients();
        if ($invoicesPaidByClients != null)
        {
            if (array_key_exists(request('client_id'), $invoicesPaidByClients) == true)
            {
                unset($invoicesPaidByClients[request('client_id')]);
            }
        }

        return view('clients._modal_edit')
            ->with('editMode', true)
            ->with('client', $client)
            ->with('refreshToRoute', request('refresh_to_route'))
            ->with('id', request('id'))
            ->with('customFields', CustomFieldsParser::getFields('clients'))
            ->with('tags', Tag::whereTagEntity('client')->pluck('name', 'name'))
            ->with('selectedTags', $selectedTags)
            ->with('leadSourceTags', ['' => trans('fi.select_lead_source_tag')] + Tag::whereTagEntity('client_lead_source')->pluck('name', 'id')->toArray())
            ->with('selectedLeadSourceTags', $client->lead_source_tag_id)
            ->with('countries', Country::getAll())
            ->with('parentClient', $client->allow_child_accounts != 0 ? true : false)
            ->with('parentClients', ['' => trans('fi.select_parent_client')] + Client::getParentClients(request('client_id')))
            ->with('invoicesPaidBy', $invoicesPaidByClients);

    }

    public function ajaxModalUpdate(ClientUpdateRequest $request, $id)
    {
        $client = Client::find($id);
        $client->fill($request->except('custom', 'tags'));
        $client->lead_source_tag_id = ($request->lead_source_tag_id == '') ? null : $request->lead_source_tag_id;
        $client->save();

        // Save the custom fields.
        $customFieldData = CustomFieldsTransformer::transform(request('custom', []), 'clients', $client);
        $client->custom->update($customFieldData);

        $tags    = $request->input('tags', []);
        $tag_ids = [];

        foreach ($tags as $tag)
        {
            $tag = Tag::firstOrNew(['name' => $tag, 'tag_entity' => 'client'])->fill(['name' => $tag, 'tag_entity' => 'client']);

            $tag->save();

            $tag_ids[] = $tag->id;
        }

        $client->deleteTags($client);

        foreach ($tag_ids as $tag_id)
        {
            $client->tags()->insert(['client_id' => $client->id, 'tag_id' => $tag_id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        }

        return response()->json(['success' => true], 200);
    }

    public function ajaxModalLookup()
    {
        return view('clients._modal_lookup')
            ->with('updateClientIdRoute', request('update_client_id_route'))
            ->with('refreshToRoute', request('refresh_to_route'))
            ->with('clients', Client::getDropDownList())
            ->with('client', Client::whereId(request('client_id'))->first())
            ->with('id', request('id'));
    }

    public function ajaxCheckName()
    {
        $client = Client::find(request('client_id'));

        if ($client)
        {
            return response()->json(['success' => true, 'client_id' => $client->id], 200);
        }

        return response()->json([
            'success' => false,
            'errors'  => ['messages' => [trans('fi.client_not_found')]],
        ], 400);
    }

    public function deleteImage($id, $columnName)
    {
        $customFields = ClientCustom::whereClientId($id)->first();

        $existingFile = 'clients' . DIRECTORY_SEPARATOR . $customFields->{$columnName};

        if (Storage::disk(CustomFieldsTransformer::STORAGE_DISK_NAME)->exists($existingFile))
        {
            try
            {
                Storage::disk(CustomFieldsTransformer::STORAGE_DISK_NAME)->delete($existingFile);

                $customFields->{$columnName} = null;
                $customFields->save();
            }
            catch (\Exception $e)
            {

            }
        }
    }

    public function showFilterTags()
    {
        $resultsPerPage   = 10;
        $selectedTags     = json_decode(request('tags', '[]'));
        $tagsMustMatchAll = request('tagsMustMatchAll', 0);

        $checkedTags = Tag::where('tag_entity', '=', 'client')
            ->whereIn('id', $selectedTags)->get();
        $allTags     = Tag::where('tag_entity', '=', 'client')
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
            return view('clients._modal_filter_tags')
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
                'html'          => view('clients._filter_tags_list')
                    ->with('selectedTags', $selectedTags)
                    ->with('tagsMustMatchAll', $tagsMustMatchAll)
                    ->with('checkedTags', $checkedTags)
                    ->with('allTags', $allTags)->render(),
                'link'          => $nextPageLink,
                'nextPageCount' => $nextPageCount,
            ]);
        }
    }

    public function emailPaymentReceiptStatus($clientId)
    {
        $client   = Client::find($clientId);
        $response = ['email_receipt' => false, 'currency_code' => $client->currency_code];
        switch ($client->automatic_email_payment_receipt)
        {
            case 'yes':
                $response['email_receipt'] = true;
                break;
            case 'no':
                $response['email_receipt'] = false;
                break;
            case 'default':
                $response['email_receipt'] = config('fi.automaticEmailPaymentReceipts');
                break;
            default:
                $response['email_receipt'] = false;
        }
        return $response;
    }

    public function showFilterColumns()
    {
        return view('clients._modal_filter_column')
            ->with('clientColumnSettings', true)
            ->with('defaultClientSequenceColumnsData', User::clientColumnSettings());
    }

    public function storeClientColumnSettings(ColumnSettingStoreRequest $request)
    {
        $columns             = request('columns');
        $client_list_columns = User::clientColumnSettings();

        if (request('columns'))
        {
            foreach ($client_list_columns as $key => $index)
            {
                refactorClientColumnSetting('clientColumnSettings' . $key, (isset($columns[$key])) ? 1 : 0);
            }

        }
        return response()->json([], 200);
    }

    public function deleteModal()
    {
        try
        {
            return view('clients._modal_client_delete')->with('url', request('action'))->with('returnURL', request('returnURL'))->with('modalName', request('modalName'))->with('isReload', request('isReload'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }

    public function deleteContactModal()
    {
        try
        {
            return view('clients._modal_delete_contact')->with('contactId', request('id'))->with('url', request('action'))->with('returnURL', request('returnURL'))->with('modalName', request('modalName'))->with('isReload', request('isReload'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }
}
