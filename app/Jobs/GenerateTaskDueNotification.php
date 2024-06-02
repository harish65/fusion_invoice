<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Jobs;

use Carbon\Carbon;
use FI\Modules\Notifications\Models\Notification;
use FI\Modules\TaskList\Models\Task;
use FI\Modules\Users\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateTaskDueNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $user;

    /**
     * @param User $user
     */
    public function __construct(User $user = NULL)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $q = Task::where([
            ['due_date', '<', Carbon::now()],
            ['is_complete', '=', 0],
        ])->whereDoesntHave('notifications', function ($query)
        {
            $query->where([
                ['action_type', '=', 'due_date_breached'],
            ]);
        });

        if ($this->user)
        {
            $q->where('user_id', $this->user->id);
        }
        
        $tasks = $q->get();
        foreach ($tasks as $task)
        {
            $notification                  = new Notification();
            $notification->user_id         = $task->assignee_id;
            $notification->notifiable_id   = $task->id;
            $notification->notifiable_type = 'FI\Modules\TaskList\Models\Task';
            $notification->action_type     = 'due_date_breached';
            $notification->type            = 'notice';
            $notification->save();
        }
    }
}
