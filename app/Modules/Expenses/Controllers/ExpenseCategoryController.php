<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Expenses\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Expenses\Models\ExpenseCategory;
use FI\Modules\Expenses\Requests\ExpenseCategoryRequest;
use FI\Traits\ReturnUrl;

class ExpenseCategoryController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();

        return view('expenses.categories.index')
            ->with('expenseCategories', ExpenseCategory::orderBy('name')->paginate(config('fi.resultsPerPage')));
    }

    public function create()
    {
        return view('expenses.categories.form');
    }

    public function store(ExpenseCategoryRequest $request)
    {
        ExpenseCategory::create($request->all());

        return redirect($this->getReturnUrl())
            ->with('alertSuccess', trans('fi.record_successfully_created'));
    }

    public function edit($id)
    {
        return view('expenses.categories.form')
            ->with('expenseCategory', ExpenseCategory::find($id));
    }

    public function update(ExpenseCategoryRequest $request, $id)
    {
        $expenseCategory = ExpenseCategory::find($id);

        $expenseCategory->fill($request->all());

        $expenseCategory->save();

        return redirect($this->getReturnUrl())
            ->with('alertSuccess', trans('fi.record_successfully_updated'));
    }

    public function delete($id)
    {
        try
        {
            ExpenseCategory::destroy($id);

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