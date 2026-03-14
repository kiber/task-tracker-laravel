<?php

declare(strict_types=1);

namespace App\Actions\Category;

use App\Models\Category;
use App\Models\User;

readonly class CreateCategory
{
    public function execute(array $categoryData, User $user): Category
    {
        return $user->categories()->create($categoryData);
    }
}
