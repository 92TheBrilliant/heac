<?php

namespace App\Policies;

use App\Models\Research;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ResearchPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_research');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Research $research): bool
    {
        return $user->can('view_research');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_research');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Research $research): bool
    {
        return $user->can('edit_research');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Research $research): bool
    {
        return $user->can('delete_research');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Research $research): bool
    {
        return $user->can('delete_research');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Research $research): bool
    {
        return $user->can('delete_research');
    }

    /**
     * Determine whether the user can publish the model.
     */
    public function publish(User $user, Research $research): bool
    {
        return $user->can('publish_research');
    }
}
