<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    /** List submissions (controller usually scopes by assignment) */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /** See one submission (or download) */
    public function view(User $user, Submission $submission): bool
    {
        // Author of the assignment OR the student who submitted (by email)
        return $user->id === $submission->assignment->author_id
            || ($submission->student_email && strcasecmp($user->email, $submission->student_email) === 0);
    }

    /** Download uses same rule as view */
    public function download(User $user, Submission $submission): bool
    {
        return $this->view($user, $submission);
    }

    /** Create a submission on an assignment (teachers creating on behalf, etc.) */
    public function create(User $user): bool
    {
        // authenticated user may create (controller/route decides *which* assignment)
        return true;
    }

    /** Edit/update (rare) */
    public function update(User $user, Submission $submission): bool
    {
        // Usually only assignment author can modify records
        return $user->id === $submission->assignment->author_id  || $user->isAdmin();
    }

    /** Delete submission */
    public function delete(User $user, Submission $submission): bool
    {
        return $user->id === $submission->assignment->author_id  || $user->isAdmin();
    }

    public function restore(User $user, Submission $submission): bool
    {
        return $user->id === $submission->assignment->author_id  || $user->isAdmin();
    }

    public function forceDelete(User $user, Submission $submission): bool
    {
        return $user->id === $submission->assignment->author_id  || $user->isAdmin();
    }
}
