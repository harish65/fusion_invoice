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

use FI\Support\DateFormatter;
use FI\Support\NumberFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class TimeTrackingTimer extends Model
{
    protected $table = 'time_tracking_timers';

    protected $guarded = ['id'];

    public function getFormattedBilledAttribute()
    {
        return ($this->attributes['billed']) ? trans('fi.yes') : trans('fi.no');
    }

    public function task()
    {
        return $this->belongsTo('Addons\TimeTracking\Models\TimeTrackingTask', 'time_tracking_task_id');
    }

    public function getFormattedEndAtAttribute()
    {
        if ($this->attributes['end_at'] <> '0000-00-00 00:00:00')
        {
            return DateFormatter::format($this->attributes['end_at'], true);
        }

        return '';
    }

    public function getFormattedHoursAttribute()
    {
        return NumberFormatter::format($this->attributes['hours']);
    }

    public function getFormattedStartAtAttribute()
    {
        return DateFormatter::format($this->attributes['start_at'], true);
    }

    public function getTenseAttribute()
    {
        $startAt = Carbon::parse($this->attributes['start_at'])->format('Y-m-d');

        switch ($startAt)
        {
            // Current Today
            case today()->format('Y-m-d'):
                $rtn = 'ct';
                break;
            // Future Tomorrow
            case Carbon::tomorrow()->format('Y-m-d'):
                $rtn = 'ft';
                break;
            // Past Yesterday
            case Carbon::yesterday()->format('Y-m-d'):
                $rtn = 'py';
                break;
            // Past Other
            case Carbon::parse($this->attributes['start_at'])->isPast():
                $rtn = 'po';
                break;
            // Future Other
            default:
                $rtn = 'fo';
        }
        return $rtn;
    }

    public function getFormattedStartAtForHumansAttribute()
    {
        $daysApart = Carbon::parse($this->attributes['start_at'])->startOfDay()->diffInDays(Carbon::now()->startOfDay());
        Carbon::setLocale(app()->getLocale());

        if ($daysApart > 1)
        {
            return Carbon::parse($this->attributes['start_at'])->diffForHumans();
        }
        else
        {
            if ($daysApart == 0)
            {
                return trans('fi.today') . ', ' . Carbon::parse($this->attributes['start_at'])->diffForHumans();
            }
            else
            {
                if (Carbon::now()->format('Y-m-d') < Carbon::parse($this->attributes['start_at'])->format('Y-m-d'))
                {
                    return trans('fi.tomorrow');
                }
                else
                {
                    return trans('fi.yesterday');
                }
            }
        }

    }

    public function getHoursAttribute()
    {
        if (!$this->formatted_end_at)
        {
            return '';
        }

        return $this->attributes['hours'];
    }
}