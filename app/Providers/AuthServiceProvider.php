<?php

namespace App\Providers;

use App\Models\Assignment;
use App\Models\Submission;
use App\Policies\AssignmentPolicy;
use App\Policies\SubmissionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Assignment::class => AssignmentPolicy::class,
        Submission::class => SubmissionPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
