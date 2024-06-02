<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\TaxRates\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\TaxRates\Models\TaxRate;
use FI\Modules\TaxRates\Requests\TaxRateRequest;
use FI\Traits\ReturnUrl;

class TaxRateController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();

        $taxRates = TaxRate::sortable(['name' => 'asc'])->paginate(config('fi.resultsPerPage'));

        return view('tax_rates.index')
            ->with('taxRates', $taxRates);
    }

    public function create()
    {
        return view('tax_rates.form')
            ->with('editMode', false);
    }

    public function store(TaxRateRequest $request)
    {
        TaxRate::create($request->all());

        return redirect($this->getReturnUrl())
            ->with('alertSuccess', trans('fi.record_successfully_created'));
    }

    public function edit($id)
    {
        $taxRate = TaxRate::find($id);

        return view('tax_rates.form')
            ->with('editMode', true)
            ->with('taxRate', $taxRate);
    }

    public function update(TaxRateRequest $request, $id)
    {
        $taxRate = TaxRate::find($id);

        $taxRate->fill($request->all());

        $taxRate->save();

        return redirect($this->getReturnUrl())
            ->with('alertSuccess', trans('fi.record_successfully_updated'));
    }

    public function delete($id)
    {
        $taxRate = TaxRate::find($id);

        if ($taxRate->in_use)
        {
            return response()->json(['success' => false, 'message' => trans('fi.cannot_delete_record_in_use')], 400);
        }
        else
        {
            try
            {
                $taxRate->delete();
                return response()->json(['success' => true, 'message' => trans('fi.record_successfully_deleted')], 200);
            }
            catch (\Exception $e)
            {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
        }

    }

    public function deleteModal()
    {
        try
        {
            return view('layouts._delete_modal')->with('url', request('action'))->with('modalName', request('modalName'))->with('isReload', request('isReload'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }
}