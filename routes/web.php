<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CvDownloadController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SavedJobController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - JobConnect Laravel 13
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. PUBLIC ROUTES
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{id}', [JobController::class, 'show'])->name('jobs.show');
Route::post('/jobs/{id}/apply', [JobController::class, 'apply'])->middleware(['role:candidate', 'permission'])->name('jobs.apply');
Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
Route::get('/companies/{slug}', [CompanyController::class, 'show'])->name('companies.show');

// ==========================================
// 2. AUTHENTICATION ROUTES (Pure Session-based)
// ==========================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.post');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('throttle:5,1')->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// ==========================================
// 2b. SHARED AUTHENTICATED ROUTES (All Active Roles)
// ==========================================
Route::middleware('active')->group(function () {
    Route::get('/account/password', [AuthController::class, 'showChangePasswordForm'])->name('account.password');
    Route::put('/account/password', [AuthController::class, 'updatePassword'])->name('account.password.update');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::get('/cvs/{id}/download', [CvDownloadController::class, 'show'])->name('cvs.download');
});

// ==========================================
// 3. CANDIDATE ROUTES (Role: candidate)
// ==========================================
Route::middleware(['role:candidate', 'permission'])->prefix('candidate')->name('candidate.')->group(function () {
    Route::get('/saved-jobs', [SavedJobController::class, 'index'])->name('saved.index');
    Route::put('/saved-jobs/{id}', [SavedJobController::class, 'store'])->name('saved.store');
    Route::delete('/saved-jobs/{id}', [SavedJobController::class, 'destroy'])->name('saved.destroy');
    Route::get('/dashboard', [CandidateController::class, 'dashboard'])->name('dashboard');
    Route::get('/applications', [CandidateController::class, 'applications'])->name('applications');
    Route::post('/applications/{id}/withdraw', [CandidateController::class, 'withdraw'])->name('applications.withdraw');
    Route::get('/cvs', [CandidateController::class, 'cvs'])->name('cvs');
    Route::post('/cvs', [CandidateController::class, 'storeCv'])->name('cvs.store');
    Route::put('/cvs/{id}', [CandidateController::class, 'updateCv'])->name('cvs.update');
    Route::delete('/cvs/{id}', [CandidateController::class, 'deleteCv'])->name('cvs.delete');
    Route::post('/cvs/{id}/primary', [CandidateController::class, 'setPrimaryCv'])->name('cvs.primary');
    Route::get('/profile', [CandidateController::class, 'profile'])->name('profile');
    Route::put('/profile', [CandidateController::class, 'updateProfile'])->name('profile.update');
});

// ==========================================
// 4. EMPLOYER ROUTES (Role: employer)
// ==========================================
Route::middleware(['role:employer', 'permission'])->prefix('employer')->name('employer.')->group(function () {
    Route::get('/dashboard', [EmployerController::class, 'dashboard'])->name('dashboard');
    Route::get('/company', [EmployerController::class, 'companyEdit'])->name('company');
    Route::put('/company', [EmployerController::class, 'companyUpdate'])->name('company.update');

    // CRUD Tin tuyển dụng
    Route::get('/jobs', [EmployerController::class, 'jobs'])->name('jobs.index');
    Route::get('/jobs/create', [EmployerController::class, 'createJob'])->name('jobs.create');
    Route::post('/jobs', [EmployerController::class, 'storeJob'])->name('jobs.store');
    Route::get('/jobs/{id}/edit', [EmployerController::class, 'editJob'])->name('jobs.edit');
    Route::put('/jobs/{id}', [EmployerController::class, 'updateJob'])->name('jobs.update');
    Route::delete('/jobs/{id}', [EmployerController::class, 'destroyJob'])->name('jobs.destroy');
    Route::post('/jobs/{id}/toggle', [EmployerController::class, 'toggleJobStatus'])->name('jobs.toggle');
    Route::post('/jobs/{id}/renew', [EmployerController::class, 'renewJob'])->name('jobs.renew');

    // Quản lý ứng viên & Phỏng vấn
    Route::get('/applications/export', [EmployerController::class, 'exportApplications'])->name('applications.export');
    Route::get('/applications/{job_id?}', [EmployerController::class, 'applications'])->name('applications');
    Route::post('/applications/{id}/status', [EmployerController::class, 'updateApplicationStatus'])->name('applications.status');
    Route::post('/applications/{id}/invite', [EmployerController::class, 'inviteInterview'])->name('applications.invite');
});

// ==========================================
// 5. ADMIN ROUTES (Role: admin)
// ==========================================
Route::middleware(['role:admin', 'permission'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/permissions', [AdminController::class, 'permissions'])->name('permissions.index');
    Route::post('/permissions', [AdminController::class, 'updatePermissions'])->name('permissions.update');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/jobs', [AdminController::class, 'jobs'])->name('jobs.index');
    Route::post('/jobs/{id}/approve', [AdminController::class, 'approveJob'])->name('jobs.approve');
    Route::post('/jobs/{id}/reject', [AdminController::class, 'rejectJob'])->name('jobs.reject');
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::post('/users/{id}/toggle', [AdminController::class, 'toggleUserStatus'])->name('users.toggle');
    Route::post('/companies/{id}/verify', [AdminController::class, 'toggleCompanyVerify'])->name('users.verify-company');
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories.index');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{id}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{id}', [AdminController::class, 'destroyCategory'])->name('categories.destroy');
});
