<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Flot;
use Illuminate\Auth\Access\HandlesAuthorization;

class FlotPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_flot');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Flot $flot): bool
    {
        return $user->can('view_flot');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_flot');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Flot $flot): bool
    {
        return $user->can('update_flot');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Flot $flot): bool
    {
        return $user->can('delete_flot');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_flot');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Flot $flot): bool
    {
        return $user->can('force_delete_flot');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_flot');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Flot $flot): bool
    {
        return $user->can('restore_flot');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_flot');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Flot $flot): bool
    {
        return $user->can('replicate_flot');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_flot');
    }
}
