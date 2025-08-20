<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Staff;
use Illuminate\Auth\Access\HandlesAuthorization;

class StaffPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin/staff permission
        if ($user->can('view_any_staff')) {
            return true;
        }

        // Allow staff to view their own profile in menu
        if ($user->can('view_on_staff_profile')) {
            return true;
        }

        return false;
    }

    public function view(User $user, Staff $staff): bool
    {
        // Admin/staff permission
        if ($user->can('view_staff')) {
            return true;
        }

        // Allow staff to view only their own record
        return $user->email === $staff->email && $user->can('view_on_staff_profile');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_staff');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Staff $staff): bool
    {
        return $user->can('update_staff');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Staff $staff): bool
    {
        return $user->can('delete_staff');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_staff');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Staff $staff): bool
    {
        return $user->can('force_delete_staff');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_staff');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Staff $staff): bool
    {
        return $user->can('restore_staff');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_staff');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Staff $staff): bool
    {
        return $user->can('replicate_staff');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_staff');
    }
}
