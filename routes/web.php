<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\QuestionPackageController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ResultController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\ExamController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('student.dashboard');
    })->name('dashboard');

    // Admin Routes
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Question Package Management
        Route::resource('packages', QuestionPackageController::class);
        Route::get('packages/{package}/builder', [QuestionPackageController::class, 'builder'])->name('packages.builder');
        Route::post('packages/{package}/add-question', [QuestionPackageController::class, 'addQuestion'])->name('packages.add-question');
        Route::delete('packages/{package}/remove-question/{question}', [QuestionPackageController::class, 'removeQuestion'])->name('packages.remove-question');
        Route::post('packages/{package}/update-order', [QuestionPackageController::class, 'updateOrder'])->name('packages.update-order');
        
        // Question Management
        Route::resource('questions', QuestionController::class);
        
        // Batch Management
        Route::resource('batches', BatchController::class);
        Route::post('batches/{batch}/generate-codes', [BatchController::class, 'generateCodes'])->name('batches.generate-codes');
        
        // User Management
        Route::resource('users', UserController::class);
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        
        // Results Management
        Route::get('results', [ResultController::class, 'index'])->name('results.index');
        Route::get('results/export', [ResultController::class, 'export'])->name('results.export');
    });

    // Student Routes
    Route::middleware(['student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [StudentDashboardController::class, 'profile'])->name('profile');
        Route::post('/profile', [StudentDashboardController::class, 'updateProfile'])->name('profile.update');
        
        // Exam Routes
        Route::get('/exam/start', [ExamController::class, 'start'])->name('exam.start');
        Route::post('/exam/begin', [ExamController::class, 'begin'])->name('exam.begin');
        Route::get('/exam/{examSession}/take', [ExamController::class, 'take'])->name('exam.take');
        Route::post('/exam/{examSession}/save-answer', [ExamController::class, 'saveAnswer'])->name('exam.save-answer');
        Route::post('/exam/{examSession}/navigate', [ExamController::class, 'navigate'])->name('exam.navigate');
        Route::get('/exam/{examSession}/next-section', [ExamController::class, 'nextSection'])->name('exam.next-section');
        Route::post('/exam/{examSession}/submit', [ExamController::class, 'submitExam'])->name('exam.submit');
        Route::get('/exam/{examSession}/completed', [ExamController::class, 'completed'])->name('exam.completed');
        Route::post('/exam/{examSession}/tab-switch', [ExamController::class, 'checkTabSwitch'])->name('exam.tab-switch');
        Route::post('/exam/{examSession}/sync-timer', [ExamController::class, 'syncTimer'])->name('exam.sync-timer');
    });
});
