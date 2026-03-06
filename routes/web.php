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

// ─── Setup & Roles Init Route (Emergency DB Fix) ──────────────────────────────
Route::get('/init-roles', function () {
    try {
        echo "Starting initialization...<br>";

        // 1. Ensure 'role' column exists
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function ($table) {
                $table->string('role')->default('admin');
            });
            echo "Added 'role' column.<br>";
        }

        // 2. Ensure 'username' column exists
        if (!Schema::hasColumn('users', 'username')) {
            Schema::table('users', function ($table) {
                $table->string('username')->unique()->nullable()->after('name');
            });
            echo "Added 'username' column.<br>";
        }

        // 3. Create/Update the Admin User
        \App\Models\User::updateOrCreate(
            ['username' => 'CCS-FCO OFFICER'],
            [
                'name' => 'CCS-FCO OFFICER',
                'email' => 'admin@ccs.essu.edu.ph',
                'password' => \Illuminate\Support\Facades\Hash::make('ccsattendanceqr-2026'),
                'role' => 'super_admin',
            ]
        );
        echo "Admin account created/updated.<br>";

        // 4. Try running migrations just in case
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            echo "Artisan migrate executed.<br>";
        } catch (\Exception $e) {
            echo "Note: Artisan migrate failed (likely already up to date): " . $e->getMessage() . "<br>";
        }

        return "<br><b>SUCCESS: System initialized. Please go to <a href='/login'>/login</a> and use the new credentials.</b>";
    } catch (\Exception $e) {
        return "<br><b>ERROR:</b> " . $e->getMessage();
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

    // AI Chatbot
    Route::post('/api/chatbot', [AdminController::class, 'askChatbot']);
});
