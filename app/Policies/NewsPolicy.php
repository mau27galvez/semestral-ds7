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
        // For this demo, allow any authenticated user to access news management
        // In production, you might want to check for specific roles or permissions
        return true;

        // Example with role-based access:
        // return $user->hasRole('admin') || $user->hasPermission('manage-news');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, News $news): bool
    {
        // Allow any authenticated user to view news
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // For this demo, allow any authenticated user to create news
        // In production, you might want to check for author/editor role
        return true;

        // Example with role-based access:
        // return $user->hasRole('author') || $user->hasPermission('create-news');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, News $news): bool
    {
        // Allow any authenticated user to update news
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, News $news): bool
    {
        // For this demo, allow any authenticated user to delete news
        // In production, you might want to check for admin role
        return true;

        // Example with role-based access:
        // return $user->hasRole('admin') || $user->hasPermission('delete-news');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, News $news): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, News $news): bool
    {
        return $this->delete($user, $news);
    }
}
