<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\EnsureOnlyOneAdmin;

// ─── Auth Routes ─────────────────────────────────────────────────────────────

// Register (only if no admin exists yet)
Route::get('/register', [AuthController::class , 'showRegister'])->middleware(EnsureOnlyOneAdmin::class)->name('register');
Route::post('/register', [AuthController::class , 'register'])->middleware(EnsureOnlyOneAdmin::class);

// Login / Logout
Route::get('/login', [AuthController::class , 'showLogin'])->name('login');
Route::post('/login', [AuthController::class , 'login']);
Route::post('/logout', [AuthController::class , 'logout'])->name('logout');

// Email Verification
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'A new verification link has been sent to your email!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// ─── Protected Dashboard ─────────────────────────────────────────────────────

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', function () {
            return view('welcome');
        }
        );
    });

// ─── JSON API Routes (also protected) ────────────────────────────────────────

Route::middleware(['auth', 'verified'])->group(function () {
    // Students
    Route::get('/api/students', [AdminController::class , 'students']);
    Route::post('/api/students', [AdminController::class , 'storeStudent']);
    Route::delete('/api/students/{id}', [AdminController::class , 'deleteStudent']);

    // Events
    Route::get('/api/events', [AdminController::class , 'events']);
    Route::post('/api/events', [AdminController::class , 'storeEvent']);

    // Sections
    Route::get('/api/sections', [AdminController::class , 'sections']);
    Route::post('/api/sections', [AdminController::class , 'storeSection']);
    Route::delete('/api/sections/{name}', [AdminController::class , 'deleteSection']);

    // Attendance
    Route::get('/api/attendance', [AdminController::class , 'attendance']);
    Route::post('/api/attendance', [AdminController::class , 'storeAttendance']);

    // Activity Logs
    Route::get('/api/logs', [AdminController::class , 'logs']);
    Route::post('/api/logs', [AdminController::class , 'storeLog']);

    // Dashboard
    Route::get('/api/dashboard', [AdminController::class , 'dashboardData']);

    // AI Masterlist Photo Scanner
    Route::post('/api/masterlist/scan', [AdminController::class , 'scanMasterlistPhoto']);
});
