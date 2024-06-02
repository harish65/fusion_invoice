<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\MailQueue\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\MailQueue\Models\MailQueue;

class MailLogController extends Controller
{
    public function index()
    {
        if (!config('app.demo'))
        {
            $mails = MailQueue::sortable(['created_at' => 'desc'])
                ->keywords(request('search'))
                ->paginate(config('fi.resultsPerPage'));

            return view('mail_log.index')
                ->with('mails', $mails)
                ->with('searchPlaceholder', trans('fi.search_log'));
        }
        else
        {
            return redirect()->route('dashboard.index')
                ->withErrors(trans('fi.functionality_not_available_on_demo'));
        }
    }

    public function content()
    {
        $mail = MailQueue::select('subject', 'body')
            ->where('id', request('id'))
            ->first();

        return view('mail_log._modal_content')
            ->with('mail', $mail);
    }

    public function delete($id)
    {
        MailQueue::destroy($id);

        return redirect()->route('mailLog.index')
            ->with('alert', trans('fi.record_successfully_deleted'));
    }

    public function deleteModal()
    {
        try
        {
            return view('layouts._delete_modal_details')->with('url', request('action'))->with('returnURL', request('returnURL'))->with('modalName', request('modalName'))->with('isReload', request('isReload'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }
}