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
use Addons\Commission\Requests\CommissionTypeRequest;
use Addons\Commission\Models\RecurringInvoiceItemCommission;
use FI\Http\Controllers\Controller;
use FI\Modules\Invoices\Models\InvoiceItem;
use FI\Traits\ReturnUrl;
use Illuminate\Http\Request;

class CommissionTypeController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();

        return view('commission.type.index')
            ->with('commission_types', CommissionType::orderBy('name')->paginate(config('fi.resultsPerPage')));

    }

    public function create()
    {
        return view('commission.type.form');
    }

    public function commissionTypes(Request $request)
    {
        return response()->json(CommissionType::find($request->type_id));
    }

    public function store(CommissionTypeRequest $request)
    {
        try
        {
            CommissionType::create($request->all());

            return redirect($this->getReturnUrl())
                ->with('alertSuccess', trans('fi.record_successfully_created'));
        }
        catch (\Exception $e)
        {
            return redirect()->back()
                ->withInput($request->input())
                ->with('alertInfo', trans('Commission::lang.formula_validate'));
        }
    }

    public function edit($id)
    {
        return view('commission.type.form')
            ->with('commissionType', CommissionType::find($id));
    }

    public function update(CommissionTypeRequest $request, $id)
    {
        $commission_type = CommissionType::find($id);
        $commission_type->fill($request->all());
        $commission_type->save();

        return redirect($this->getReturnUrl())
            ->with('alertSuccess', trans('fi.record_successfully_updated'));
    }

    public function delete($id)
    {

        try
        {
            $typeInUse = InvoiceItemCommission::whereTypeId($id)->count();
            $typeInUse += RecurringInvoiceItemCommission::whereTypeId($id)->count();
            if ($typeInUse > 0)
            {
                return response()->json(['success' => false, 'message' => trans('Commission::lang.cannot_delete_type')], 400);

            }
            else
            {
                CommissionType::destroy($id);

                return response()->json(['success' => true, 'message' => trans('fi.record_successfully_deleted')], 200);

            }

        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

    }

    public function deleteModal()
    {
        try
        {
            return view('layouts._delete_modal')->with('message', request('message'))->with('url', request('action'))->with('modalName', request('modalName'))->with('isReload', request('isReload'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }

}