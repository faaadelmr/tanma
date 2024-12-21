<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\TaskCategoryController;
use App\Http\Controllers\MeetingController;

Route::get('/', function () {
    return view('auth.login');
});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', [DailyReportController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/export/', [DailyReportController::class, 'exportToExcel'])->name('daily-reports.export');
});


Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserManagementController::class);
    Route::resource('task-categories', TaskCategoryController::class);
});

Route::middleware(['auth'])->prefix('daily-reports')->name('daily-reports.')->group(function () {
    Route::get('/', [DailyReportController::class, 'index'])->name('index');
    Route::get('/create', [DailyReportController::class, 'create'])->name('create');
    Route::post('/', [DailyReportController::class, 'store'])->name('store');
    Route::get('/{dailyReport}', [DailyReportController::class, 'show'])->name('show');
    Route::get('/{dailyReport}/continue', [DailyReportController::class, 'continue'])->name('continue');
    //Route::put('/{dailyReport}', [DailyReportController::class, 'update'])->name('update');
    Route::delete('/{dailyReport}', [DailyReportController::class, 'destroy'])->name('destroy');
    Route::post('/{dailyReport}/approve', [DailyReportController::class, 'approve'])->name('approve');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/meetings', [MeetingController::class, 'index'])->name('meetings.index');
    Route::post('/meetings/generate', [MeetingController::class, 'generateMeetings'])->name('meetings.generate');
    Route::post('/meetings/{meeting}/topics', [MeetingController::class, 'storeTopic'])->name('meetings.topics.store');
    Route::patch('/topics/{topic}/toggle', [MeetingController::class, 'toggleComplete'])->name('topics.toggle');
    Route::post('/topics/{topic}/continue', [MeetingController::class, 'continueTopic'])->name('topics.continue');
});



require __DIR__.'/auth.php';

