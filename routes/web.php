<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\SubmissionController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('login');
});

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth')->group(function () {

    // Dashboard / Profile
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile',   [UserController::class, 'profile'])->name('profile');
    Route::patch('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::patch('/password', [UserController::class, 'changePassword'])->name('password.change');

    // Assignments
    Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/create', [AssignmentController::class, 'create'])->name('assignments.create');
    Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
    Route::get('/assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');
    Route::get('/assignments/{assignment}/edit', [AssignmentController::class, 'edit'])->name('assignments.edit');
    Route::patch('/assignments/{assignment}', [AssignmentController::class, 'update'])->name('assignments.update');
    Route::post('/assignments/{assignment}/close', [AssignmentController::class, 'close'])->name('assignments.close');
    Route::post('/assignments/{assignment}/open',  [AssignmentController::class, 'open'])->name('assignments.open');
    Route::delete('/assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');

    // Submissions
    Route::get('/assignments/{assignment}/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
    Route::post('/assignments/{assignment}/submissions', [SubmissionController::class, 'store'])->name('submissions.store');
    Route::get('/assignments/{assignment}/submissions/{submission}/edit', [SubmissionController::class, 'edit'])->name('submissions.edit');
    Route::patch('/assignments/{assignment}/submissions/{submission}', [SubmissionController::class, 'update'])->name('submissions.update');
    Route::get('/submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
    Route::get('/submissions/{submission}/download', [SubmissionController::class, 'download'])->name('submissions.download');
    Route::delete('/submissions/{submission}', [SubmissionController::class, 'destroy'])->name('submissions.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get(
        '/assignments/{assignment}/submissions/merged.pdf',
        [SubmissionController::class, 'mergedPdf']
    )->name('assignments.merged-pdf');
});

//  public intake page by assignment code:
Route::get('/a/{assignment:code}', [SubmissionController::class, 'createPublic'])->name('assignments.intake');
Route::post('/a/{assignment:code}', [SubmissionController::class, 'store'])->name('assignments.submit');
