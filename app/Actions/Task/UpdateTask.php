<?php

declare(strict_types=1);

namespace App\Actions\Task;

use App\Actions\Category\ResolveCategory;
use App\Models\Task;
use App\Models\User;

readonly class UpdateTask
{
    public function __construct(
        private ResolveCategory $resolveCategory
    ) {}

    public function execute(array $taskData, User $user, Task $task): Task
    {
        $taskData['category_id'] = $this->resolveCategory->execute($taskData['category_id'] ?? '', $user);

        $task->fill($taskData);
        $task->save();

        return $task;
    }
}
