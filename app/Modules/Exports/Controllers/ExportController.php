<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Exports\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Exports\Models\ExportMapping;
use FI\Modules\Exports\Support\Export;
use FI\Modules\Exports\Requests\MappingRequest;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function index()
    {
        return view('export.index');
    }

    public function populateForm($type)
    {
        return view('export.form')
            ->with('exportType', $type)
            ->with('exportLbl', ExportMapping::getLabelByType($type))
            ->with('fields', ExportMapping::getAllFieldsByType($type))
            ->with('mappingOptions', ExportMapping::getMappingsByType($type))
            ->with('defaultMapping', ExportMapping::getDefaultMappingByType($type))
            ->with('format', ['CSV' => 'CSV', 'JSON' => 'JSON', 'XLS' => 'XLS', 'XML' => 'XML']);
    }

    public function export(Request $request, $exportType)
    {
        if (!config('app.demo'))
        {
            $fields = ($request->has('fields')) ? $request->get('fields') : [];
            $export = new Export($exportType, $request->get('format'), $fields);

            $export->writeFile();

            return response()->download($export->getDownloadPath());
        }
        else
        {
            return redirect()->route('export.index')
                ->withErrors(trans('fi.functionality_not_available_on_demo'));
        }
    }

    public function saveMapping(MappingRequest $request)
    {
        $input = $request->all();
        try
        {
            if (!config('app.demo'))
            {
                $exportMapping = ExportMapping::updateOrCreate(['name' => $request->get('name'), 'type' => $request->get('type')], $input);
                if ($exportMapping->is_default == 1)
                {
                    ExportMapping::where(
                        [
                            ['is_default', '=', '1'],
                            ['type', '=', $exportMapping->type],
                            ['id', '!=', $exportMapping->id],
                        ]
                    )->update(['is_default' => 0]);
                }
            }
            else
            {
                return response()->json(['success' => false, 'message' => trans('fi.functionality_not_available_on_demo')], 200);
            }
        }
        catch (QueryException $e)
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
        return response()->json(['success' => true, 'data' => $exportMapping, 'message' => trans('fi.record_successfully_created')], 200);

    }

    public function changeMapping(Request $request)
    {
        return ExportMapping::find($request->id);
    }

    public function deleteMapping(Request $request, $id, $type)
    {
        try
        {
            if (!config('app.demo'))
            {
                if (ExportMapping::destroy($id) == true)
                {
                    $defaultMapping = ExportMapping::getDefaultMappingByType($type);
                    return response()->json(['success' => true, 'data' => $defaultMapping, 'message' => trans('fi.record_successfully_deleted')], 200);
                }
                else
                {
                    return response()->json(['success' => false, 'message' => trans('fi.record_not_found')], 400);
                }
            }
            else
            {
                return response()->json(['success' => false, 'message' => trans('fi.functionality_not_available_on_demo')], 200);
            }
        }
        catch (Exception $e)
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    public function deleteModal()
    {
        try
        {
            return view('export._delete_modal')
                ->with('url', request('action'))
                ->with('message', request('message'))
                ->with('selectedMappingId', request('selectedMappingId'))
                ->with('returnURL', request('returnURL'))
                ->with('modalName', request('modalName'))
                ->with('isReload', request('isReload'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }
}