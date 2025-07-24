<?php

namespace App\Policies;

use App\Models\News;
use App\Models\User;

class NewsPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only users who can manage news can access news management
        return $user->canManageNews();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, News $news): bool
    {
        // Allow users who can manage news to view individual news items
        return $user->canManageNews();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Editors, supervisors, and admins can create news
        return $user->canManageNews();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, News $news): bool
    {
        // Editors, supervisors, and admins can update news
        return $user->canManageNews();
    }

    /**
     * Determine whether the user can publish the model.
     */
    public function publish(User $user, News $news): bool
    {
        // Only supervisors and admins can publish news
        return $user->canPublishNews();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, News $news): bool
    {
        // Only admins can delete news
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, News $news): bool
    {
        // Only admins can restore deleted news
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, News $news): bool
    {
        // Only admins can permanently delete news
        return $user->isAdmin();
    }
}
