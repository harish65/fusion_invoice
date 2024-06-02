<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Seeder\Seeds;

use Carbon\Carbon;
use Faker;
use FI\Modules\Clients\Models\Client;
use FI\Modules\TaskList\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    protected $count;

    protected $faker;

    public function __construct($count = 0)
    {
        $this->count = $count;
        $this->faker = Faker\Factory::create();
    }

    public function run()
    {
        $tasks = [];
        for ($i = 0; $i < $this->count; $i++)
        {
            $toTasks = Task::create([
                'user_id'     => auth()->id(),
                'client_id'   => Client::inRandomOrder()->first()->id,
                'title'       => $this->faker->text,
                'description' => $this->faker->realText,
                'due_date'    => Carbon::now()->addDays(30),
                'assignee_id' => auth()->id(),
                'is_complete' => 0,
            ]);

            $tasks[] = $toTasks;

        }
        return $tasks;
    }
}
