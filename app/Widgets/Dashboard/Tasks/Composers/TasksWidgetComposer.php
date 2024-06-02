<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Widgets\Dashboard\Tasks\Composers;

use FI\Modules\TaskList\Models\TaskSection;

class TasksWidgetComposer
{
    public function compose($view)
    {
        $view->with('taskSections', $this->taskSections())
             ->with('taskFilters', ['my_tasks' => trans('fi.my_tasks'), 'assigned_to_others' => trans('fi.assigned_to_others'), 'assigned_from_others' => trans('fi.assigned_from_others')]);
    }

    private function taskSections()
    {
        return TaskSection::all()->pluck('slug', 'id')->toArray();
    }

}