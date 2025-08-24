<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;

class AssignmentPolicy
{
    /** List my assignments */
    public function viewAny(User $user): bool
    {
        return true; // any authenticated user can list their own (controller scopes by author_id)
    }

    /** See one assignment */
    public function view(User $user, Assignment $assignment): bool
    {
        return $user->id === $assignment->author_id || $user->isAdmin();
    }

    /** Create new assignment */
    public function create(User $user): bool
    {
        return true; // authenticated check handled by middleware
    }

    /** Update assignment (incl. open/close) */
    public function update(User $user, Assignment $assignment): bool
    {
        return $user->id === $assignment->author_id  || $user->isAdmin();
    }

    /** Delete assignment */
    public function delete(User $user, Assignment $assignment): bool
    {
        return $user->id === $assignment->author_id  || $user->isAdmin();
    }

    /** (Optional) Restore / force delete if you use soft deletes on assignments */
    public function restore(User $user, Assignment $assignment): bool
    {
        return $user->id === $assignment->author_id  || $user->isAdmin();
    }

    public function forceDelete(User $user, Assignment $assignment): bool
    {
        return $user->id === $assignment->author_id  || $user->isAdmin();
    }
}
