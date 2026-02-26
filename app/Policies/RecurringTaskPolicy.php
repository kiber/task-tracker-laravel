<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\RecurringTask;
use App\Models\User;

class RecurringTaskPolicy
{
    /**
     * Determine whether the user can manage the model.
     */
    public function manage(User $user, RecurringTask $recurringTask): bool
    {
        return $user->id === $recurringTask->user_id;
    }
}
