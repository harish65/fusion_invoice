<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\SystemLog\Controllers;

use Illuminate\Support\Facades\File;
use Exception;

class SystemLogController
{
    public function index()
    {
        if (!config('app.demo'))
        {
            try
            {
                return view('system_log.index')
                    ->with(['logs' => logReader(storage_path('logs/laravel.log'))])
                    ->with(['logFileExist' => true]);
            }
            catch (Exception $e)
            {
                return view('system_log.index')
                    ->with(['logs' => null])
                    ->with(['errorMessage' => trans('fi.system_log_is_missing_or_empty')])
                    ->with(['logFileExist' => false]);
            }
        }
        else
        {
            return redirect()->route('dashboard.index')
                ->withErrors(trans('fi.functionality_not_available_on_demo'));
        }
    }

    public function systemLogClearModal()
    {
        return view('system_log._modal_clear_system_log');
    }

    public function systemLogClear()
    {
        try
        {
            File::put(storage_path('logs/laravel.log'), '');
            return response()->json(['success' => true, 'message' => trans('fi.system_log_clear_successfully')], 200);
        }
        catch (Exception $e)
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}