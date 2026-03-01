<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\EnsureOnlyOneAdmin;

// ─── Auth Routes ─────────────────────────────────────────────────────────────

// Registration & Verification Disabled for built-in admin account.
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ─── Password Reset Routes (Disabled) ──────────────────────────────────────────

// ─── Setup & Roles Init Route ────────────────────────────────────────────────
Route::get('/init-roles', function () {
    try {
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table(
                'users',
                function ($table) {
                    $table->string('role')->default('admin');
                }
            );
        }
        return 'Roles initialized.';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});


// ─── Protected Dashboard ─────────────────────────────────────────────────────

Route::middleware(['auth'])->group(function () {
    Route::get(
        '/',
        function () {
            return view('welcome');
        }
    );
});

// ─── JSON API Routes (also protected) ────────────────────────────────────────

Route::middleware(['auth'])->group(function () {
    // Students
    Route::get('/api/students', [AdminController::class, 'students']);
    Route::post('/api/students', [AdminController::class, 'storeStudent']);
    Route::delete('/api/students/{id}', [AdminController::class, 'deleteStudent']);

    // Events
    Route::get('/api/events', [AdminController::class, 'events']);
    Route::post('/api/events', [AdminController::class, 'storeEvent']);

    // Year And Sections
    Route::get('/api/year-and-sections', [AdminController::class, 'yearAndSections']);
    Route::post('/api/year-and-sections', [AdminController::class, 'storeYearAndSection']);
    Route::delete('/api/year-and-sections/{name}', [AdminController::class, 'deleteYearAndSection']);

    // Attendance
    Route::get('/api/attendance', [AdminController::class, 'attendance']);
    Route::post('/api/attendance', [AdminController::class, 'storeAttendance']);

    // Activity Logs
    Route::get('/api/logs', [AdminController::class, 'logs']);
    Route::post('/api/logs', [AdminController::class, 'storeLog']);

    // Dashboard
    Route::get('/api/dashboard', [AdminController::class, 'dashboardData']);

    // AI Masterlist Photo Scanner
    Route::post('/api/masterlist/scan', [AdminController::class, 'scanMasterlistPhoto']);
    Route::post('/api/students/batch', [AdminController::class, 'storeBatchStudents']);
});
