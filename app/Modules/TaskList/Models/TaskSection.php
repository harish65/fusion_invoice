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

use Illuminate\Database\Eloquent\Model;

class TaskSection extends Model
{
    protected $table = 'task_section';

    protected $guarded = ['id'];

    /*
     |--------------------------------------------------------------------------
     | Static Methods
     |--------------------------------------------------------------------------
    */

    public static function getTaskSectionList()
    {
        return self::pluck('slug', 'id')->all();
    }

    public static function getTaskSectionListByName()
    {
        return self::pluck('name', 'id')->all();
    }

}
