<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\TimeTracking\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PresetTaskItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'time_tracking_preset_tasks_id' =>trans('TimeTracking::lang.task_list_name'),
            'task_name'                     =>trans('TimeTracking::lang.new_item_name'),
        ];
    }

    public function rules()
    {
        return [
            'time_tracking_preset_tasks_id' => 'required',
            'task_name'                     => 'required',
        ];
    }
}