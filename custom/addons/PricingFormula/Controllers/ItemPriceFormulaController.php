<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\PricingFormula\Controllers;

use FI\Http\Controllers\Controller;
use Addons\PricingFormula\Models\ItemPriceFormula;
use Addons\PricingFormula\Requests\ItemPriceFormulaRequest;
use FI\Traits\ReturnUrl;

class ItemPriceFormulaController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();

        return view('pricing_formula.formula.index')
            ->with('itemPriceFormulas', ItemPriceFormula::sortable(['name' => 'asc'])->paginate(config('fi.resultsPerPage')));
    }

    public function create()
    {
        return view('pricing_formula.formula.form');
    }

    public function store(ItemPriceFormulaRequest $request)
    {
        ItemPriceFormula::create($request->all());

        return redirect($this->getReturnUrl())
            ->with('alertSuccess', trans('fi.record_successfully_created'));
    }

    public function edit($id)
    {
        return view('pricing_formula.formula.form')
            ->with('itemPriceFormula', ItemPriceFormula::find($id));
    }

    public function update(ItemPriceFormulaRequest $request, $id)
    {
        $ItemPriceFormula = ItemPriceFormula::find($id);

        $ItemPriceFormula->fill($request->all());

        $ItemPriceFormula->save();

        return redirect($this->getReturnUrl())
            ->with('alertSuccess', trans('fi.record_successfully_updated'));
    }

    public function delete($id)
    {
        try
        {
            ItemPriceFormula::destroy($id);
            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_deleted')], 200);

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
            return view('layouts._delete_modal')->with('url', request('action'))->with('modalName', request('modalName'))->with('isReload', request('isReload'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }

}