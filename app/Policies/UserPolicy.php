<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // For this demo, allow any authenticated user to access user management
        // In production, you might want to check for specific roles or permissions
        return true;

        // Example with role-based access:
        // return $user->hasRole('admin') || $user->hasPermission('manage-users');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Users can view their own profile or if they have admin access
        return $user->id === $model->id || $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // For this demo, allow any authenticated user to create users
        // In production, you might want to check for admin role
        return true;

        // Example with role-based access:
        // return $user->hasRole('admin') || $user->hasPermission('create-users');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Users can update their own profile or if they have admin access
        return $user->id === $model->id || $this->viewAny($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Prevent users from deleting themselves
        if ($user->id === $model->id) {
            return false;
        }

        // For this demo, allow any authenticated user to delete other users
        // In production, you might want to check for admin role
        return true;

        // Example with role-based access:
        // return $user->hasRole('admin') || $user->hasPermission('delete-users');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $this->delete($user, $model);
    }
}
