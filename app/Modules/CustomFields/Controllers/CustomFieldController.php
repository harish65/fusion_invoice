<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\CustomFields\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\CustomFields\Models\CustomField;
use FI\Modules\CustomFields\Requests\CustomFieldReorderRequest;
use FI\Modules\CustomFields\Requests\CustomFieldStoreRequest;
use FI\Modules\CustomFields\Requests\CustomFieldUpdateRequest;
use FI\Modules\CustomFields\Support\CustomFields;
use FI\Traits\ReturnUrl;
use Illuminate\Http\Request;

class CustomFieldController extends Controller
{
    use ReturnUrl;

    public function index()
    {

        $customFields = CustomField::sortable(['display_order' => 'asc', 'tbl_name' => 'asc', 'field_label' => 'asc'])->groupBy('tbl_name', 'column_name')->get();

        return view('custom_fields.index')
            ->with('customFields', $customFields)
            ->with('selectedTab', request('table', 'clients'))
            ->with('tableNames', CustomFields::tableNames());
    }

    public function create()
    {

        return view('custom_fields.form')
            ->with('editMode', false)
            ->with('selectedTable', request('table', 'clients'))
            ->with('tableNames', CustomFields::tableNames())
            ->with('fieldTypes', CustomFields::fieldTypes())
            ->with('fieldWithoutMeta', CustomFields::fieldsWithoutMeta());
    }

    public function store(CustomFieldStoreRequest $request)
    {

        $input = $request->except('table');

        $input['column_name'] = CustomField::getNextColumnName($input['tbl_name']);

        CustomField::create($input);

        CustomField::createCustomColumn($input['tbl_name'], $input['column_name'], $input['field_type']);

        return redirect()
            ->route('customFields.index', ['table' => $request->get('table', 'clients')])
            ->with('alertSuccess', trans('fi.record_successfully_created'));
    }

    public function edit($id)
    {

        $customField = CustomField::find($id);

        return view('custom_fields.form')
            ->with('editMode', true)
            ->with('customField', $customField)
            ->with('selectedTable', $customField->tbl_name)
            ->with('tableNames', CustomFields::tableNames())
            ->with('fieldTypes', CustomFields::fieldTypes())
            ->with('fieldWithoutMeta', CustomFields::fieldsWithoutMeta());
    }

    public function update(CustomFieldUpdateRequest $request, $id)
    {
        $customField          = CustomField::find($id);
        $input                = $request->except('tbl_name', 'table', 'is_required');
        $input['is_required'] = $request->get('is_required', null);

        $customField->fill($input);

        $customField->save();

        return redirect()
            ->route('customFields.index', ['table' => $request->get('table', 'clients')])
            ->with('alertSuccess', trans('fi.record_successfully_updated'));
    }

    public function delete($id)
    {
        $customField = CustomField::find($id);
        $table       = $customField->tbl_name;

        CustomField::deleteCustomColumn($customField->tbl_name, $customField->column_name);

        CustomField::destroy($id);

        return redirect()
            ->route('customFields.index', ['table' => $table])
            ->with('alert', trans('fi.record_successfully_deleted'));
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->get('ids');

        foreach ($ids as $id)
        {
            $customField = CustomField::find($id);

            CustomField::deleteCustomColumn($customField->tbl_name, $customField->column_name);

            CustomField::destroy($id);
        }

    }

    public function reorder(CustomFieldReorderRequest $request)
    {
        $ids  = $request->get('ids');
        $type = $request->get('type');
        foreach ($ids as $key => $id)
        {
            CustomField::whereTblName($type)->whereId($id)
                       ->update([
                           'display_order' => $key,
                       ]);
        }
    }

    public function deleteModal()
    {
        try
        {
            return view('layouts._delete_modal_details')
                ->with('url', request('action'))
                ->with('returnURL', request('returnURL'))
                ->with('modalName', request('modalName'))
                ->with('isReload', request('isReload'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }

    public function deleteBulkCustomFieldsModal()
    {
        try
        {
            return view('custom_fields._bulk_custom_fields_delete_modal')
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
}
