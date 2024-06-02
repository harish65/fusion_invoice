<?php

namespace Addons\SimpleTodo\Models;

use FI\Support\DateFormatter;
use Illuminate\Database\Eloquent\Model;

class SimpleTodoTask extends Model
{
    protected $table = 'simpletodo_tasks';

    protected $guarded = ['id'];

    protected $dates = ['start_at', 'due_at'];

    public function getStartAtAttribute($value)
    {
        return DateFormatter::format($value);
    }

    public function getDueAtAttribute($value)
    {
        if ($value != '0000-00-00')
        {
            return DateFormatter::format($value);
        }

        return '';
    }

    public function setStartAtAttribute($value)
    {
        $this->attributes['start_at'] = DateFormatter::unformat($value);
    }

    public function setDueAtAttribute($value)
    {
        $this->attributes['due_at'] = DateFormatter::unformat($value);
    }
}