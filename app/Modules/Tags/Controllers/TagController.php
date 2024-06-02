<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Tags\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Tags\Models\Tag;
use FI\Modules\Tags\Requests\TagUpdateRequest;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{

    public function delete()
    {
        if (!config('app.demo'))
        {
            try
            {
                Tag::whereTagEntity('client_lead_source')->doesntHave('clientLeadSourceTags')->delete();

                Tag::whereTagEntity('client')->doesntHave('clientTags')->delete();

                Tag::whereTagEntity('note')->doesntHave('noteTags')->delete();

                Tag::whereTagEntity('sales')->doesntHave('invoiceTags')->doesntHave('recurringInvoiceTags')->delete();

                DB::statement('DELETE ct1 FROM client_tags ct1 INNER JOIN client_tags ct2 WHERE ct1.id < ct2.id AND (ct1.client_id = ct2.client_id AND ct1.tag_id = ct2.tag_id)');
            }
            catch (Exception $e)
            {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }

            return response()->json(['success' => true, 'message' => trans('fi.orphan_tags_deleted')], 200);
        }
        else
        {
            return response()->json(['success' => false, 'message' => trans('fi.functionality_not_available_on_demo')], 401);
        }
    }

    public function editModal()
    {
        try
        {
            return view('tags._tags_modal')
                ->with('modalName', request('modalName'))
                ->with('category', ['' => trans('fi.select_tag_category')] + Tag::getTagsCategory());

        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }

    public function updateModal(TagUpdateRequest $request)
    {
        try
        {
            $tag       = Tag::find(request('tag_id'));
            $tag->name = request('tag_name_update');
            $tag->save();
            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_updated')], 200);
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }

    public function categoryWiseData()
    {
        try
        {
            $categoryWiseRecords = ['' => trans('fi.tagselection')] + Tag::getTagsCategoryWiseRecords(request('category'));
            return response()->json(['success' => true, 'count' => count($categoryWiseRecords), 'categoryWiseRecords' => $categoryWiseRecords], 200);
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }

    }
}