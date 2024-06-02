<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Attachments\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Attachments\Events\AddTransition;
use FI\Modules\Attachments\Events\CheckAttachment;
use FI\Modules\Attachments\Models\Attachment;
use FI\Modules\Clients\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttachmentController extends Controller
{
    public function download($urlKey)
    {
        try
        {
            $attachment = Attachment::where('url_key', $urlKey)->firstOrFail();

            $headers = [
                'Content-Type'        => $attachment->mimetype,
                'Content-Length'      => $attachment->size,
                'Content-Disposition' => 'attachment; filename=' . $attachment->filename,
            ];

            return response($attachment->content, 200, $headers);
        }
        catch (\Exception $e)
        {
            return view('errors.link_expired');
        }

    }

    public function ajaxList()
    {
        $model = request('model');

        $object = $model::find(request('model_id'));

        return view('attachments._table')
            ->with('model', request('model'))
            ->with('modelId', request('model_id'))
            ->with('object', $object);
    }

    public function ajaxDelete()
    {
        try
        {
            $client     = Client::whereId(request('client_id'))->first()->toArray() ?: null;
            $attachment = Attachment::find(request('attachment_id'));
            event(new AddTransition($attachment, 'deleted', $client));
            $attachment->delete();
            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_deleted')], 200);

        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('fi.unknown_error')], 400);
        }
    }

    public function ajaxModal()
    {
        return view('attachments._modal_attach_files')
            ->with('model', request('model'))
            ->with('modelId', request('model_id'));
    }

    public function ajaxUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'attachments.*' => 'file|max:8192',
        ]);

        if ($validator->fails())
        {
            $iniPostMaxSize = returnBytes(ini_get('post_max_size'));
            if ($iniPostMaxSize < 8388608)
            {
                $iniPostMaxSizeInMB = number_format($iniPostMaxSize / (1024 * 1024), 2) . 'MB';
                return response()->json(['success' => false, 'message' => trans('fi.attachment_error', ['size' => $iniPostMaxSizeInMB])], 422);
            }
            else
            {
                return response()->json(['success' => false, 'message' => trans('fi.attachment_error', ['size' => '8MB'])], 422);
            }

        }
        $model = request('model');

        $object = $model::find(request('model_id'));

        event(new CheckAttachment($object));
    }

    public function ajaxAccessUpdate()
    {
        $attachment = Attachment::find(request('attachment_id'));

        $attachment->client_visibility = request('client_visibility');

        $attachment->save();
    }


    public function deleteModal()
    {
        try
        {
            return view('attachments._delete_attachments_modal')
                ->with('url', request('action'))
                ->with('modalName', request('modalName'))
                ->with('isReload', request('isReload'))
                ->with('returnURL', request('returnURL'))
                ->with('model', request('model'))
                ->with('modelId', request('model_id'))
                ->with('clientId', request('client_id'))
                ->with('attachmentId', request('attachment_id'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }
}