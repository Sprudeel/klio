<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;

class AssignmentPolicy
{
    /** See one assignment */
    public function view(User $user, Assignment $assignment): bool
    {
        return $user->id === $assignment->author_id || $user->isAdmin();
    }

    /** See ones assignments */
    public function index(): bool {
        return true;
    }

    /** See ALL assignments */
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

    public function export(User $user, Assignment $assignment): bool
    {
        return $user->id === $assignment->author_id || $user->isAdmin();
    }
}
