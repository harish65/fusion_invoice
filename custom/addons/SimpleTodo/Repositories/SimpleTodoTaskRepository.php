<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\SimpleTodo\Repositories;

use Addons\SimpleTodo\Models\SimpleTodoTask;

class SimpleTodoTaskRepository
{
    public function find($id)
    {
        return SimpleTodoTask::find($id);
    }

    public function paginate()
    {
        return SimpleTodoTask::orderBy('due_at', 'desc')->paginate(config('fi.resultsPerPage'));;
    }

    public function create($input)
    {
        $task = new SimpleTodoTask();

        $task->fill($input);

        return $task->save();
    }

    public function update($input, $id)
    {
        $task = SimpleTodoTask::find($id);

        $task->fill($input);

        return $task->save();
    }

    public function delete($id)
    {
        SimpleTodoTask::destroy($id);
    }
}