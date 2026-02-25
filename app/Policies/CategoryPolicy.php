<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    /**
     * Determine whether the user can manage the model.
     */
    public function manage(User $user, Category $category): bool
    {
//        return $user->is($category->user); // this will execute additional query
//        return $category->user()->is($user); // this will NOT execute additional query
        return $user->id === $category->user_id;
    }
}
