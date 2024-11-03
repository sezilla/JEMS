<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TaskCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskCategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_task::category');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TaskCategory $taskCategory): bool
    {
        return $user->can('view_task::category');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_task::category');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TaskCategory $taskCategory): bool
    {
        return $user->can('update_task::category');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaskCategory $taskCategory): bool
    {
        return $user->can('delete_task::category');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_task::category');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, TaskCategory $taskCategory): bool
    {
        return $user->can('force_delete_task::category');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_task::category');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, TaskCategory $taskCategory): bool
    {
        return $user->can('restore_task::category');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_task::category');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, TaskCategory $taskCategory): bool
    {
        return $user->can('replicate_task::category');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_task::category');
    }
}
