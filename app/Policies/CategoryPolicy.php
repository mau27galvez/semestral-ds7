<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // For this demo, allow any authenticated user to access category management
        // In production, you might want to check for specific roles or permissions
        return true;

        // Example with role-based access:
        // return $user->hasRole('admin') || $user->hasPermission('manage-categories');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Category $category): bool
    {
        // Allow any authenticated user to view categories
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // For this demo, allow any authenticated user to create categories
        // In production, you might want to check for admin role
        return true;

        // Example with role-based access:
        // return $user->hasRole('admin') || $user->hasPermission('create-categories');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Category $category): bool
    {
        // Allow any authenticated user to update categories
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Category $category): bool
    {
        // For this demo, allow any authenticated user to delete categories
        // In production, you might want to check for admin role
        return true;

        // Example with role-based access:
        // return $user->hasRole('admin') || $user->hasPermission('delete-categories');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Category $category): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Category $category): bool
    {
        return $this->delete($user, $category);
    }
}
