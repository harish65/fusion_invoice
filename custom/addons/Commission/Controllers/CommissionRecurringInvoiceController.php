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
use Addons\Commission\Models\RecurringInvoiceItemCommission;
use Addons\Commission\Requests\RecurringInvoiceItemCommissionRequest;
use FI\Http\Controllers\Controller;
use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\Mru\Events\MruLog;
use FI\Modules\RecurringInvoices\Models\RecurringInvoiceItem;
use FI\Traits\ReturnUrl;
use FI\Support\DateFormatter;

class CommissionRecurringInvoiceController extends Controller
{

    use ReturnUrl;

    public function index()
    {

        $this->setReturnUrl();

        event(new MruLog(['module' => 'commissions', 'action' => 'view', 'id' => '', 'title' => trans('Commission::lang.recurring_invoice_commission')]));

        $recurringInvoiceItemCommissions = RecurringInvoiceItemCommission::select('recurring_invoice_item_commissions.*',
            'clients.name as client_name', 'recurring_invoice_item_commissions.amount as commission', 'recurring_invoice_item_amounts.subtotal',
            'commission_types.name as commission_type', 'users.name as username')
                                                                         ->join('recurring_invoice_items', 'recurring_invoice_items.id', '=', 'recurring_invoice_item_commissions.recurring_invoice_item_id')
                                                                         ->join('recurring_invoices', 'recurring_invoices.id', '=', 'recurring_invoice_items.recurring_invoice_id')
                                                                         ->join('clients', 'clients.id', '=', 'recurring_invoices.client_id')
                                                                         ->join('commission_types', 'recurring_invoice_item_commissions.type_id', '=', 'commission_types.id')
                                                                         ->join('recurring_invoice_item_amounts', 'recurring_invoice_items.id', '=', 'recurring_invoice_item_amounts.item_id')
                                                                         ->join('users', 'recurring_invoice_item_commissions.user_id', '=', 'users.id')
                                                                         ->with(['invoiceItem.amount', 'invoiceItem.commissions', 'invoiceItem.commissions.type', 'invoiceItem.commissions.user', 'invoiceItem.recurringInvoice'])
                                                                         ->sortable()
                                                                         ->keywords(request('search'))
                                                                         ->companyProfileId(request('company_profile'))
                                                                         ->status(request('status', 'active'))
                                                                         ->paginate(config('fi.resultsPerPage'));

        return view('commission.recurring.index')
            ->with('companyProfiles', ['' => trans('fi.all_company_profiles')] + CompanyProfile::getList())
            ->with('filterStatuses', ['' => trans('fi.all_statuses')] + RecurringInvoiceItemCommission::getStatusList())
            ->with('searchPlaceholder', trans('Commission::lang.search_commissions'))
            ->with('recurringInvoiceItemCommissions', $recurringInvoiceItemCommissions);

    }

    public function load($id)
    {
        $invoiceItems = RecurringInvoiceItem::with(['amount', 'commissions', 'commissions.type', 'commissions.user', 'recurringInvoice'])
                                            ->where('recurring_invoice_id', $id)
                                            ->paginate(config('fi.resultsPerPage'));
        return view('commission.recurring._table')
            ->with('invoiceItems', $invoiceItems);
    }

    public function create($id)
    {
        return view('commission.recurring._modal_create')
            ->with('commissionType', CommissionType::getDropDownList())
            ->with('commission', RecurringInvoiceItemCommission::latest()->first())
            ->with('items', RecurringInvoiceItem::whereRecurringInvoiceId($id)->pluck('name', 'id')->all())
            ->with('users', InvoiceItemCommission::getUserDropDownList());
    }

    public function store(RecurringInvoiceItemCommissionRequest $request)
    {
        $input              = $request->all();
        $input['stop_date'] = DateFormatter::unformat($input['stop_date']);
        RecurringInvoiceItemCommission::create($input);
        return redirect($this->getReturnUrl())
            ->with('alertSuccess', trans('fi.record_successfully_created'));
    }

    public function edit($id, $invoice_id)
    {

        return view('commission.recurring._modal_edit')
            ->with('commission', RecurringInvoiceItemCommission::find($id))
            ->with('users', InvoiceItemCommission::getUserDropDownList())
            ->with('items', RecurringInvoiceItem::whereRecurringInvoiceId($invoice_id)->pluck('name', 'id')->all())
            ->with('commissionType', CommissionType::getDropDownList());

    }

    public function update(RecurringInvoiceItemCommissionRequest $request, $id)
    {
        $input                          = $request->all();
        $input['stop_date']             = DateFormatter::unformat($input['stop_date']);
        $recurringInvoiceItemCommission = RecurringInvoiceItemCommission::find($id);
        $recurringInvoiceItemCommission->fill($input);
        $recurringInvoiceItemCommission->save();
        return redirect($this->getReturnUrl())
            ->with('alertSuccess', trans('fi.record_successfully_updated'));
    }

    public function delete($id)
    {
        RecurringInvoiceItemCommission::destroy($id);
        return redirect($this->getReturnUrl())
            ->with('alert', trans('fi.record_successfully_deleted'));
    }

    public function bulkDelete()
    {
        RecurringInvoiceItemCommission::destroy(request('ids'));
    }

}
