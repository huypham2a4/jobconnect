<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - JobConnect Laravel 12
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. PUBLIC ROUTES
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{id}', [JobController::class, 'show'])->name('jobs.show');
Route::post('/jobs/{id}/apply', [JobController::class, 'apply'])->name('jobs.apply');

// ==========================================
// 2. AUTHENTICATION ROUTES (Pure Session-based)
// ==========================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// 3. CANDIDATE ROUTES (Role: candidate)
// ==========================================
Route::middleware(['role:candidate'])->prefix('candidate')->name('candidate.')->group(function () {
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
Route::middleware(['role:employer'])->prefix('employer')->name('employer.')->group(function () {
    Route::get('/dashboard', [EmployerController::class, 'dashboard'])->name('dashboard');
    Route::get('/company', [EmployerController::class, 'companyEdit'])->name('company');
    Route::put('/company', [EmployerController::class, 'companyUpdate'])->name('company.update');
    
    // CRUD Tin tuyển dụng
    Route::get('/jobs', [EmployerController::class, 'jobs'])->name('jobs.index');
    Route::get('/jobs/create', [EmployerController::class, 'createJob'])->name('jobs.create');
    Route::post('/jobs', [EmployerController::class, 'storeJob'])->name('jobs.store');
    Route::get('/jobs/{id}/edit', [EmployerController::class, 'editJob'])->name('jobs.edit');
    Route::put('/jobs/{id}', [EmployerController::class, 'updateJob'])->name('jobs.update');
    Route::post('/jobs/{id}/toggle', [EmployerController::class, 'toggleJobStatus'])->name('jobs.toggle');
    Route::post('/jobs/{id}/renew', [EmployerController::class, 'renewJob'])->name('jobs.renew');
    
    // Quản lý ứng viên & Phỏng vấn
    Route::get('/applications/{job_id?}', [EmployerController::class, 'applications'])->name('applications');
    Route::post('/applications/{id}/status', [EmployerController::class, 'updateApplicationStatus'])->name('applications.status');
    Route::post('/applications/{id}/invite', [EmployerController::class, 'inviteInterview'])->name('applications.invite');
});

// ==========================================
// 5. ADMIN ROUTES (Role: admin)
// ==========================================
Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/jobs', [AdminController::class, 'jobs'])->name('jobs.index');
    Route::post('/jobs/{id}/approve', [AdminController::class, 'approveJob'])->name('jobs.approve');
    Route::post('/jobs/{id}/reject', [AdminController::class, 'rejectJob'])->name('jobs.reject');
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::post('/users/{id}/toggle', [AdminController::class, 'toggleUserStatus'])->name('users.toggle');
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories.index');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
});