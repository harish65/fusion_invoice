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

use Addons\Commission\Events\AddInvoiceItemCommissionTransition;

use Addons\Commission\Models\CommissionType;
use Addons\Commission\Models\InvoiceItemCommission;
use Addons\Commission\Requests\InvoiceItemCommissionRequest;
use FI\Http\Controllers\Controller;
use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\Invoices\Models\InvoiceItem;
use FI\Modules\Mru\Events\MruLog;
use FI\Traits\ReturnUrl;


class CommissionInvoiceController extends Controller
{

    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();

        event(new MruLog(['module' => 'commissions', 'action' => 'view', 'id' => '', 'title' => trans('Commission::lang.commission')]));

        $invoiceItemCommission = InvoiceItemCommission::select('invoice_item_commissions.*', 'invoices.number', 'clients.name as client_name',
            'invoice_item_commissions.amount as commission', 'invoice_item_commissions.status', 'invoice_item_amounts.subtotal',
            'commission_types.name as commission_type', 'users.name as username')
            ->join('invoice_items', 'invoice_items.id', '=', 'invoice_item_commissions.invoice_item_id')
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->join('clients', 'clients.id', '=', 'invoices.client_id')
            ->join('commission_types', 'invoice_item_commissions.type_id', '=', 'commission_types.id')
            ->join('invoice_item_amounts', 'invoice_items.id', '=', 'invoice_item_amounts.item_id')
            ->join('users', 'invoice_item_commissions.user_id', '=', 'users.id')
            ->with(['invoiceItem.amount', 'invoiceItem.commissions', 'invoiceItem.commissions.type', 'invoiceItem.commissions.user', 'invoiceItem.invoice'])
            ->sortable()
            ->status(request('status'))
            ->keywords(request('search'))
            ->companyProfileId(request('company_profile'))
            ->paginate(config('fi.resultsPerPage'));

        return view('commission.invoice.index')
            ->with('filterStatuses', ['' => trans('fi.all_statuses')] + InvoiceItemCommission::getStatusList())
            ->with('bulkStatuses', InvoiceItemCommission::getBulkStatusList())
            ->with('companyProfiles', ['' => trans('fi.all_company_profiles')] + CompanyProfile::getList())
            ->with('searchPlaceholder', trans('Commission::lang.search_commissions'))
            ->with('invoiceItemCommission', $invoiceItemCommission);

    }

    public function load($id)
    {
        $invoiceItems = InvoiceItem::with(['amount', 'commissions', 'commissions.type', 'commissions.user', 'invoice'])
            ->has('commissions')
            ->whereInvoiceId($id)->paginate(config('fi.resultsPerPage'));
        return view('commission.invoice._table')
            ->with('invoiceItems', $invoiceItems);

    }

    public function create($id)
    {
        return view('commission.invoice._modal_create')
            ->with('commissionType', CommissionType::getDropDownList())
            ->with('commission', InvoiceItemCommission::latest()->first())
            ->with('users', InvoiceItemCommission::getUserDropDownList())
            ->with('items', InvoiceItem::whereInvoiceId($id)->pluck('name', 'id')->all())
            ->with('status', InvoiceItemCommission::getStatusList());
    }

    public function store(InvoiceItemCommissionRequest $request)
    {

        InvoiceItemCommission::create($request->all());

        return redirect($this->getReturnUrl())
            ->with('alertSuccess', trans('fi.record_successfully_created'));
    }

    public function edit($id, $invoice_id)
    {
        return view('commission.invoice._modal_edit')
            ->with('commission', InvoiceItemCommission::find($id))
            ->with('commissionType', CommissionType::getDropDownList())
            ->with('users', InvoiceItemCommission::getUserDropDownList())
            ->with('items', InvoiceItem::whereInvoiceId($invoice_id)->pluck('name', 'id')->all())
            ->with('status', InvoiceItemCommission::getStatusList());
    }

    public function update(InvoiceItemCommissionRequest $request, $id)
    {
        $invoiceItemCommission = InvoiceItemCommission::find($id);
        $invoiceItemCommission->fill($request->all());
        $invoiceItemCommission->save();
        return redirect($this->getReturnUrl())
            ->with('alertSuccess', trans('fi.record_successfully_updated'));
    }

    public function delete($id)
    {
        try
        {
            InvoiceItemCommission::destroy($id);
            return response()->json(['success' => false, 'message' => trans('fi.record_successfully_deleted')], 200);

        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('fi.commission_delete_error')], 400);

        }

    }

    public function bulkDelete()
    {
        try
        {
            InvoiceItemCommission::destroy(request('ids'));
            return response()->json(['success' => false, 'message' => trans('fi.record_successfully_deleted')], 200);

        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('fi.commission_delete_error')], 400);

        }
    }

    public function bulkStatus()
    {
        $ids = request('ids');
        foreach ($ids as $id)
        {
            $nvoiceItemCommission         = InvoiceItemCommission::find($id);
            $nvoiceItemCommission->status = request('status');
            $nvoiceItemCommission->save();
        }
    }
}
