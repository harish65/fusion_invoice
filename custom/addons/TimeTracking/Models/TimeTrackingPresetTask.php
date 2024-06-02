<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\TimeTracking\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TimeTrackingPresetTask extends Model
{
    protected $table = 'time_tracking_preset_tasks';

    protected $guarded = ['id'];

    public static function getList()
    {
        return ['' => trans('TimeTracking::lang.select_preset_task')] + self::select('id', 'list_name')->orderBy('list_name')->pluck('list_name', 'id')->all();
    }

    public static function taskItemsCount()
    {
        $taskItems      = self::select('time_tracking_preset_tasks.id', 'time_tracking_preset_tasks.list_name', DB::raw('COUNT(time_tracking_preset_task_items.id) as items_count'))
            ->leftJoin('time_tracking_preset_task_items', 'time_tracking_preset_tasks.id', '=', 'time_tracking_preset_task_items.time_tracking_preset_tasks_id')
            ->groupBy('time_tracking_preset_tasks.id', 'time_tracking_preset_tasks.list_name')
            ->orderBy('time_tracking_preset_tasks.list_name')
            ->get()->toArray();
        $taskItemsCount = [];
        foreach ($taskItems as $taskItem)
        {
            $taskItemsCount[$taskItem['id']] = ["list_name" => $taskItem['list_name'], "items_count" => $taskItem['items_count']];
        }

        return $taskItemsCount;
    }

    public function item()
    {
        return $this->hasMany('Addons\TimeTracking\Models\TimeTrackingPresetTaskItem', 'time_tracking_preset_tasks_id')->orderBy('display_order');
    }
}