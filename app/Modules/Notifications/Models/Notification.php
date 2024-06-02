<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Notifications\Models;

use FI\Modules\TaskList\Models\Task;
use FI\Modules\Users\Models\User;
use FI\Support\DateFormatter;
use FI\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use Sortable;

    protected $table = 'notifications';

    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo('FI\Modules\Users\Models\User')->withTrashed();
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'notifiable_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedCreatedAtAttribute()
    {
        return DateFormatter::formatTimeAgo($this->created_at, true);
    }

    public function getFormattedUpdatedAtAttribute()
    {
        return DateFormatter::formatTimeAgo($this->updated_at, true);
    }

    public function getFormattedViewedAtAttribute()
    {
        return DateFormatter::formatTimeAgo($this->viewed_at, true);
    }

    public function getNotificationClassAttribute()
    {
        switch ($this->type)
        {
            case 'notice':
                $color = "text-warning";
                break;
            case 'info':
                $color = "text-info";
                break;
            case 'error':
                $color = "text-danger";
                break;
            default:
                $color = "text-info";
        }
        return $color;
    }

    public function getBackgroundClassAttribute()
    {
        switch ($this->type)
        {
            case 'notice':
                $color = "text-warning";
                break;
            case 'info':
                $color = "text-info";
                break;
            case 'error':
                $color = "text-danger";
                break;
            default:
                $color = "text-info";
        }
        return $color;
    }

    public function getNotificationDetailAttribute()
    {
        $text       = [];
        $notifiable = $this->notifiable;
        if ($this->notifiable_type == 'FI\Modules\TaskList\Models\Task')
        {
            if ($this->action_type == 'created')
            {
                $text['info'] = trans('fi.notification.task.created');
                $title        = $notifiable->title;
                $url          = route('task.show', $this->attributes['notifiable_id']);
                $text['link'] = '<a href="javascript:void(0);" class="notification-item dropdown-item text-wrap" data-url = ' . $url . ' data-notification-id = ' . $this->attributes['id'] . '> <i class="fa fa-tasks text-yellow"></i> ' . (isset($title) ? $title : '') . '</a>';
            }
            elseif ($this->action_type == 'due_date_breached')
            {
                $text['info'] = trans('fi.notification.task.due_date_breached', ['task_title' => $notifiable->title]);
                $title        = trans('fi.notification.task.due_date_breached', ['task_title' => $notifiable->title]);
                $url          = route('task.show', $this->attributes['notifiable_id']);
                $text['link'] = '<a href="javascript:void(0);" class="notification-item dropdown-item text-wrap" data-url = ' . $url . ' data-notification-id = ' . $this->attributes['id'] . '> <i class="fa fa-tasks text-yellow"></i> ' . (isset($title) ? $title : '') . '</a>';
            }
            elseif ($this->action_type == 'completed')
            {
                $text['info'] = trans('fi.notification.task.completed', ['user' => $this->task->assignee->formatted_name, 'task_title' => $notifiable->title]);
                $title        = trans('fi.notification.task.completed', ['user' => $this->task->assignee->formatted_name, 'task_title' => $notifiable->title]);
                $url          = route('task.show', $this->attributes['notifiable_id']);
                $text['link'] = '<a href="javascript:void(0);" class="notification-item dropdown-item text-wrap" data-url = ' . $url . ' data-notification-id = ' . $this->attributes['id'] . '> <i class="fa fa-tasks text-success"></i> ' . (isset($title) ? $title : '') . '</a>';
            }
        }
        elseif ($this->notifiable_type == 'FI\Modules\Quotes\Models\Quote')
        {
            if ($this->action_type == 'approved')
            {
                $title        = trans('fi.notification.quote.approved', ['quote_number' => $notifiable->number]);
                $url          = route('invoices.edit', ['id' => $notifiable->invoice_id]);
                $text['link'] = '<a href="javascript:void(0);" class="notification-item dropdown-item text-wrap" data-url = ' . $url . ' data-notification-id = ' . $this->attributes['id'] . '> <i class="fa fa-file-alt text-yellow"></i> ' . (isset($title) ? $title : '') . '</a>';
            }
            elseif ($this->action_type == 'quote_to_invoice')
            {
                $title        = trans('fi.notification.quote.quote_to_invoice', ['quote_number' => $notifiable->number, 'invoice_number' => $notifiable->invoice->number]);
                $url          = route('invoices.edit', ['id' => $notifiable->invoice_id]);
                $text['link'] = '<a href="javascript:void(0);" class="notification-item dropdown-item text-wrap" data-url = ' . $url . ' data-notification-id = ' . $this->attributes['id'] . '> <i class="fa fa-file-alt text-yellow"></i> ' . (isset($title) ? $title : '') . '</a>';
            }
            if ($this->action_type == 'rejected')
            {
                $title        = trans('fi.notification.quote.rejected', ['quote_number' => $notifiable->number]);
                $url          = route('quotes.edit', ['id' => $notifiable->id]);
                $text['link'] = '<a href="javascript:void(0);" class="notification-item dropdown-item text-wrap" data-url = ' . $url . ' data-notification-id = ' . $this->attributes['id'] . '> <i class="fa fa-ban text-danger"></i> ' . (isset($title) ? $title : '') . '</a>';
            }
        }
        elseif ($this->notifiable_type == null)
        {
            $taskDetail = json_decode($this->detail);
            if ($this->action_type == 'cron_start')
            {
                $title        = $taskDetail->message;
                $text['link'] = '<a href="javascript:void(0);" class="notification-item dropdown-item text-wrap" data-url = "" data-notification-id = ' . $this->attributes['id'] . '> <i class="fa fa-hourglass-start text-yellow"></i> ' . (isset($title) ? $title : '') . '</a>';
            }
            elseif ($this->action_type == 'cron_completed')
            {
                $title        = $taskDetail->message;
                $text['link'] = '<a href="javascript:void(0);" class="notification-item dropdown-item text-wrap" data-url = "" data-notification-id = ' . $this->attributes['id'] . '> <i class="fa fa-check-circle text-info"></i> ' . (isset($title) ? $title : '') . '</a>';
            }
            elseif ($this->action_type == 'cron_failed')
            {
                $title        = $taskDetail->message;
                $text['link'] = '<a href="javascript:void(0);" class="notification-item dropdown-item text-wrap"  data-url = ' . route('systemLog.index') . ' data-notification-id = ' . $this->attributes['id'] . '> <i class="fa fa-exclamation-triangle text-danger"></i> ' . (isset($title) ? $title : '') . '</a>';
            }

        }
        return $text;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeUserId($query, $user_id)
    {
        if (auth()->user()->user_type == 'admin')
        {
            $systemUserId = User::whereUserType('system')->first()->id;
            return $query->whereIn('user_id', [$user_id, $systemUserId]);
        }
        else
        {
            return $query->where('user_id', $user_id);
        }
    }
}