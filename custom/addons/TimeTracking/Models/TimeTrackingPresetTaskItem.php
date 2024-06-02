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

class TimeTrackingPresetTaskItem extends Model
{
    protected $table = 'time_tracking_preset_task_items';

    protected $guarded = ['id'];

    public function presetTask()
    {
        return $this->belongsTo('Addons\TimeTracking\Models\TimeTrackingPresetTask', 'time_tracking_preset_tasks_id','id');
    }

    public static function getList()
    {
        return self::pluck('task_name', 'id')->all();
    }

    public static function getPresetTaskItems($id)
    {
        return self::whereTimeTrackingPresetTasksId($id)->orderBY('display_order')->get();
    }
}