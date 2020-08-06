<?php

namespace App\TwUtils\TaskAdd;

use App\Task;
use App\User;
use App\SocialUser;
use App\TwUtils\UserManager;

class Factory
{
    protected $user;
    protected $task;

    public function __construct(string $operationClassName, array $settings, Task $relatedTask = null, User $user, $managedByTaskId = null)
    {
        $this->user = $user;

        $socialUser = $this->resolveUser((new $operationClassName)->getScope());

        $this->task = Task::create(
            [
                'targeted_task_id'   => $relatedTask ? $relatedTask->id : null,
                'socialuser_id'      => $socialUser->id,
                'type'               => $operationClassName,
                'status'             => 'queued',
                'extra'              => ['settings' => $settings],
                'managed_by_task_id' => $managedByTaskId,
            ]
        );
    }

    public function resolveUser($taskScope) : SocialUser
    {
        return UserManager::resolveUser($this->user, $taskScope);
    }

    public function getTask() : Task
    {
        return $this->task;
    }
}
