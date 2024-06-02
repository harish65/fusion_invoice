<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\TaskList\Models;

use FI\Modules\Clients\Models\Client;
use FI\Modules\Users\Models\User;
use FI\Support\DateFormatter;
use FI\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Task extends Model
{
    use Sortable;

    protected $table = 'tasks';

    protected $guarded = ['id'];

    protected $sortable = ['id', 'clients.name', 'title', 'description', 'due_date', 'completed_at'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function attachments()
    {
        return $this->morphMany('FI\Modules\Attachments\Models\Attachment', 'attachable');
    }

    public function notes()
    {
        return $this->morphMany('FI\Modules\Notes\Models\Note', 'notable');
    }

    public function notifications()
    {
        return $this->morphMany('FI\Modules\Notifications\Models\Notification', 'notifiable');
    }

    public function taskSection()
    {
        return $this->belongsTo(TaskSection::class, 'task_section_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getAttachmentPermissionOptionsAttribute()
    {
        return [
            '0' => trans('fi.not_visible'),
            '1' => trans('fi.visible'),
        ];
    }

    public function getFormattedDueDateAttribute()
    {
        return $this->due_date != null && $this->due_date != '0000-00-00 00:00:00' ? DateFormatter::format($this->due_date, config('fi.includeTimeInTaskDueDate') == 1 ? true : false) : '';
    }

    public function getOverdueAttribute()
    {
        return $this->due_date != null && $this->due_date != '0000-00-00 00:00:00' ? Carbon::parse($this->due_date)->startOfDay() < Carbon::createMidnightDate() : '';
    }

    public function getDueTodayAttribute()
    {
        return $this->due_date != null && $this->due_date != '0000-00-00 00:00:00' ? Carbon::parse($this->due_date)->startOfDay() == Carbon::createMidnightDate() : '';
    }

    public function getDueDateEpochAttribute()
    {
        return $this->due_date != null && $this->due_date != '0000-00-00 00:00:00' ? DateFormatter::format($this->due_date, config('fi.includeTimeInTaskDueDate') == 1 ? true : false) : '';
    }

    public function getFormattedAssigneeAttribute()
    {
          return isset($this->assignee->id) ? ($this->assignee->id == auth()->id() ? trans('fi.me') : $this->assignee->name) : '';
    }

    public function getFormattedShortTitleAttribute()
    {
        $title = $this->title;
        if (mb_strlen($title) > 40)
        {
            $title = wordwrap($title, 40);
            $title = mb_substr($title, 0, strpos($title, "\n"));
        }

        return mb_substr($title, 0, 40) . '...';
    }

    public function getFormattedCreatedAtAttribute()
    {
        return DateFormatter::format($this->attributes['created_at'], true);
    }

    public function getFormattedCompletedDateAttribute()
    {
        return $this->completed_at != null && $this->completed_at != '0000-00-00 00:00:00' ? DateFormatter::format($this->completed_at, config('fi.includeTimeInTaskDueDate') == 1 ? true : false) : '';
    }

    public function getFormattedAsCreatedAtAttribute()
    {
        if (Carbon::parse($this->attributes['created_at'])->diffInDays(Carbon::now()) <= 7)
        {
            Carbon::setLocale(app()->getLocale());
            return Carbon::parse($this->attributes['created_at'])->diffForHumans();
        }
        else
        {
            return DateFormatter::format($this->attributes['created_at'], true);
        }
    }

    public function getFormattedAsDueDateAttribute()
    {
        $daysApart   = Carbon::parse($this->attributes['due_date'])->startOfDay()->diffInDays(Carbon::now()->startOfDay());
        $futureDue   = false;
        $datePortion = Carbon::parse($this->attributes['due_date'])->format('Y-m-d');
        $timePortion = "";
        if (config('fi.includeTimeInTaskDueDate') == 1)
        {
            $timePortion = date('h:i A', strtotime($this->attributes['due_date']));
        }

        Carbon::setLocale(app()->getLocale());

        if (Carbon::now()->format('Y-m-d') < Carbon::parse($this->attributes['due_date'])->format('Y-m-d'))
        {
            $futureDue = true;
        }

        if ($daysApart > 1)
        {
            if ($daysApart < 31 && !empty($timePortion))
            {
                return $datePortion . ' ' . $timePortion;
            }
            else
            {
                return Carbon::parse($this->attributes['due_date'])->diffForHumans();
            }
        }
        else
        {

            if ($daysApart == 0)
            {
                return 'today ' . $timePortion;
            }
            else
            {
                if (Carbon::now()->format('Y-m-d') < Carbon::parse($this->attributes['due_date'])->format('Y-m-d'))
                {
                    return 'tomorrow ' . $timePortion;
                }
                else
                {
                    return 'yesterday ' . $timePortion;
                }
            }
        }

    }

    public function getFormattedDescriptionAttribute()
    {
        if ($this->description && strlen($this->description) > 40)
        {
            return '<span data-toggle="tooltip" title="' . $this->description . '">' . mb_substr($this->description, 0, 40) . '...' . '</span>';
        }
        elseif ($this->description && strlen($this->description) < 40)
        {
            return $this->description;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeStatus($query, $status)
    {
        switch ($status)
        {
            case 'all':
                break;
            case 'closed':
                $query->where('is_complete', '=', '1');
                break;
            case 'overdue':
                $query->where('due_date', '<', date('Y-m-d'))->where('is_complete', '!=', '1');
                break;
            case 'open':
            default:
                $query->where('is_complete', '!=', '1');
        }

        return $query;
    }

    public function scopeTaskFilters($query, $taskFilter)
    {
        if (auth()->user()->user_type === 'admin')
        {
            switch ($taskFilter)
            {
                case 'my_tasks':
                    $query->whereAssigneeId(auth()->user()->id);
                    break;
                case 'assigned_to_others':
                    $query->where('tasks.user_id', auth()->user()->id)->where('tasks.assignee_id', '!=', auth()->user()->id);
                    break;
                case 'assigned_from_others':
                    $query->whereAssigneeId(auth()->user()->id)->where('tasks.user_id', '!=', auth()->user()->id);
                    break;
                case 'all_tasks':
                default:
            }
        }
        else
        {
            $query->ownTasks(auth()->user()->id);
        }

        return $query;
    }

    public function scopeSort($query, $sortBy, $sortOrder)
    {

        switch ($sortBy)
        {
            case 'due_date':
            default:
                $query->orderBy('due_date', $sortOrder);
        }

        return $query;
    }

    public function scopeKeywords($query, $keywords, $description, $title, $client, $assignee)
    {
        if ($keywords)
        {
            $query->leftJoin('users', 'tasks.assignee_id', '=', 'users.id')
                ->leftJoin('clients', 'tasks.client_id', '=', 'clients.id');

            $query->where(function ($query) use ($keywords, $description, $title, $client, $assignee)
            {
                if ($title)
                {
                    $query->orWhereRaw("CONCAT_WS('^',LOWER(title)) LIKE ?", '%' . $keywords . '%');
                }
                if ($description)
                {
                    $query->orWhereRaw("CONCAT_WS('^',LOWER(description)) LIKE ?", '%' . $keywords . '%');
                }
                if ($assignee)
                {
                    $query->orWhereRaw("CONCAT_WS('^',LOWER(users.name)) LIKE ?", '%' . $keywords . '%');
                }
                if ($client)
                {
                    $query->orWhereRaw("CONCAT_WS('^',LOWER(clients.name)) LIKE ?", '%' . $keywords . '%');
                }

            });
        }

        return $query;
    }

    public function scopeOwnTasks($query, $user_id)
    {

        $query->where(function ($query) use ($user_id)
        {
            $query->orWhere('tasks.user_id', $user_id)->orWhere('assignee_id', $user_id);
        });

        return $query;
    }

    public function scopeTaskSections($query, $section_ids = [])
    {

        $query->where(function ($query) use ($section_ids)
        {
            foreach ($section_ids as $section_id)
            {
                $query->orWhere('task_section_id', $section_id);
            }

        });

        return $query;
    }

    public function scopeDateRange($query, $from, $to)
    {
        if (empty($from) || empty($to))
        {
            return $query;
        }

        $query->where(function ($query) use ($from, $to)
        {
            $query->where('due_date', '>=', $from);
            $query->where('due_date', '<=', $to);
        });

        return $query;
    }

    public function scopeAssignee($query, $assignee)
    {
        switch ($assignee)
        {
            case 'my_tasks':
                $query->whereAssigneeId(auth()->user()->id);
                break;
            case 'assigned_to_others':
                $query->where('tasks.user_id', auth()->user()->id)->where('tasks.assignee_id', '!=', auth()->user()->id);
                break;
            case 'assigned_from_others':
                $query->whereAssigneeId(auth()->user()->id)->where('tasks.user_id', '!=', auth()->user()->id);
                break;
            case 'all_tasks':
            default:
        }

        return $query;
    }

    public function scopeRecurNow($query)
    {
        $query->where('next_date', '<>', '0000-00-00');
        $query->where('next_date', '<=', date('Y-m-d'));

        $query->where(function ($q)
        {
            $q->where('due_date', '0000-00-00');
            $q->orWhere('next_date', '<=', DB::raw('due_date'));
        });

        return $query;
    }

    public function scopeActiveClientTasks($query)
    {
        return $query->whereHas('client', function ($query)
        {
            $query->where('clients.active', '=', '1');
        });
    }

    /*
   |--------------------------------------------------------------------------
   | Static Methods
   |--------------------------------------------------------------------------
   */
    public static function getHeaders()
    {
        return ['id','client', 'title', 'description', 'due_date', 'assignee', 'completed', 'status'];
    }

}