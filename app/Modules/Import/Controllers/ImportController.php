<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Import\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Import\Importers\ImportFactory;
use FI\Modules\Import\Models\ImportMapping;
use FI\Modules\Import\Requests\ImportRequest;
use FI\Modules\Import\Requests\MappingRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function index()
    {
        $importType  = (request()->get('importType')) ? request()->get('importType') : null;
        $importTypes = [
            'clients'      => trans('fi.clients'),
            'quotes'       => trans('fi.quotes'),
            'quoteItems'   => trans('fi.quote_items'),
            'invoices'     => trans('fi.invoices'),
            'invoiceItems' => trans('fi.invoice_items'),
            'payments'     => trans('fi.payments'),
            'expenses'     => trans('fi.expenses'),
            'itemLookups'  => trans('fi.item_lookups'),
        ];

        return view('import.index')
            ->with('importTypes', $importTypes)
            ->with('importType', $importType);
    }

    public function upload(Request $request)
    {
        if (!config('app.demo'))
        {
            $request->file('import_file')->move(storage_path(), $request->input('import_type') . '.csv');

            return redirect()->route('import.map', [$request->input('import_type')]);
        }
        else
        {
            return redirect()->route('import.index')
                           ->withErrors(trans('fi.functionality_not_available_on_demo'));
        }

    }

    public function mapImport($importType)
    {
        $importer = ImportFactory::create($importType);

        return view('import.map')
            ->with('importType', $importType)
            ->with('mappingOptions', ImportMapping::getMappingsByType($importType))
            ->with('defaultMapping', ImportMapping::getDefaultMappingByType($importType))
            ->with('importFields', $importer->getFields($importType))
            ->with('fileFields', $importer->getFileFields(storage_path($importType . '.csv')));
    }

    public function exampleImport($importType)
    {
        $fileName = $importType . '_import' . '.csv';
        try
        {
            $file    = base_path('assets' . DIRECTORY_SEPARATOR . 'importers' . DIRECTORY_SEPARATOR . $fileName);
            $headers = [
                'Content-Type' => 'text/csv',
            ];
            return response()->download($file, $fileName, $headers);
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('fi.file_not_found', ['file_name' => $fileName])], 400);
        }
    }

    public function mapImportSubmit($importType)
    {
        $importer = ImportFactory::create($importType);

        if (!$importer->validateMap(request()->all()))
        {
            return redirect()->route('import.map', [$importType])
                ->withErrors($importer->errors())
                ->withInput();
        }

        $result = $importer->importData(request()->except('_token', 'mapping'));

        if (!$result['success'])
        {
            return redirect()->route('import.map', [$importType])
                ->withErrors($importer->errors());
        }

        return redirect()->route('import.index', ['importType' => $importType])
            ->with('alertSuccess', trans('fi.records_imported_successfully',['total_records' => $result['total_records']]));
    }

    public function saveMapping(MappingRequest $request)
    {
        $input = $request->all();
        $new   = true;
        try
        {
            if ($request->id)
            {
                $new = false;
                ImportMapping::whereId($request->id)->update($input);
                $importMapping = ImportMapping::find($request->id);
                $message       = trans('fi.record_successfully_updated');
            }
            else
            {
                $importMapping = ImportMapping::updateOrCreate(['name' => $request->get('name'), 'type' => $request->get('type')], $input);
                $message       = trans('fi.record_successfully_created');
            }

            if ($importMapping->is_default == 1)
            {
                ImportMapping::where(
                    [
                        ['is_default', '=', '1'],
                        ['type', '=', $importMapping->type],
                        ['id', '!=', $importMapping->id],
                    ]
                )->update(['is_default' => 0]);
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

        return response()->json(['success' => true, 'data' => $importMapping, 'isNew' => $new, 'message' => $message, 'data' => $importMapping], 200);

    }

    public function changeMapping(Request $request)
    {
        return ImportMapping::find($request->id);
    }

    public function deleteMapping(Request $request, $id, $type)
    {
        try
        {
            if (ImportMapping::destroy($id) == true)
            {
                $defaultMapping = ImportMapping::getDefaultMappingByType($type);
                return response()->json(['success' => true, 'data' => $defaultMapping, 'message' => trans('fi.record_successfully_deleted')], 200);
            }
            else
            {
                return response()->json(['success' => false, 'message' => trans('fi.record_not_found')], 400);
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
            return view('import._delete_modal')
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