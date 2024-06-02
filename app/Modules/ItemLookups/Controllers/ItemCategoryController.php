<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\ItemLookups\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\ItemLookups\Models\ItemCategory;
use FI\Modules\ItemLookups\Requests\ItemCategoryRequest;
use FI\Traits\ReturnUrl;

class ItemCategoryController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();

        return view('item_lookups.categories.index')
            ->with('itemCategories', ItemCategory::sortable(['name' => 'asc'])->paginate(config('fi.resultsPerPage')));
    }

    public function create()
    {
        return view('item_lookups.categories.form');
    }

    public function store(ItemCategoryRequest $request)
    {
        ItemCategory::create($request->all());

        return redirect($this->getReturnUrl())
            ->with('alertSuccess', trans('fi.record_successfully_created'));
    }

    public function edit($id)
    {
        return view('item_lookups.categories.form')
            ->with('itemCategory', ItemCategory::find($id));
    }

    public function update(ItemCategoryRequest $request, $id)
    {
        $itemCategory = ItemCategory::find($id);

        $itemCategory->fill($request->all());

        $itemCategory->save();

        return redirect($this->getReturnUrl())
            ->with('alertSuccess', trans('fi.record_successfully_updated'));
    }

    public function delete($id)
    {
        try
        {
            ItemCategory::destroy($id);

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