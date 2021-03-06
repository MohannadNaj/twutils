<?php

namespace App\Jobs\Actions;

use App\Models\Task;
use App\Models\Export;
use App\TwUtils\Base\Job;

class TaskCreated extends Job
{
    protected Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task->fresh();
    }

    public function handle()
    {
        $operationInstance = (new $this->task->type());

        $operationInstance
            ->setSocialUser($this->task->socialUser)
            ->setTask($this->task)
            ->setData($this->task->extra)
            ->dispatch();

        $operationInstance->initJob();

        Export::create([
            'task_id' => $this->task->id,
            'type'    => Export::TYPE_HTML,
        ]);
        Export::create([
            'task_id' => $this->task->id,
            'type'    => Export::TYPE_EXCEL,
        ]);

        if (! in_array($this->task->type, Task::TWEETS_LISTS_WITH_ENTITIES_TYPES)) {
            return;
        }

        Export::create([
            'task_id' => $this->task->id,
            'type'    => Export::TYPE_HTMLENTITIES,
        ]);
    }
}
