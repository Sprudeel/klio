<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;

class AssignmentPolicy
{
    /** See ones assignment */
    public function view(User $user, Assignment $assignment): bool
    {
        return $user->id === $assignment->author_id || $user->isAdmin();
    }

    /** See all assignments */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /** Create new assignment */
    public function create(User $user): bool
    {
        return true; // authenticated check handled by middleware
    }

    /** Update assignment (incl. open/close) */
    public function update(User $user, Assignment $assignment): bool
    {
        return $user->id === $assignment->author  || $user->isAdmin();
    }

    /** Delete assignment */
    public function delete(User $user, Assignment $assignment): bool
    {
        return $user->id === $assignment->author  || $user->isAdmin();
    }

    /** (Optional) Restore / force delete if you use soft deletes on assignments */
    public function restore(User $user, Assignment $assignment): bool
    {
        return $user->id === $assignment->author  || $user->isAdmin();
    }

    public function forceDelete(User $user, Assignment $assignment): bool
    {
        return $user->id === $assignment->author  || $user->isAdmin();
    }
}
