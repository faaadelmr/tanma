<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\DailyReportController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth'])->group(function () {
    Route::resource('users', UserManagementController::class);
});

Route::middleware(['auth'])->prefix('daily-reports')->name('daily-reports.')->group(function () {
    Route::get('/', [DailyReportController::class, 'index'])->name('index');
    Route::get('/create', [DailyReportController::class, 'create'])->name('create');
    Route::post('/', [DailyReportController::class, 'store'])->name('store');
    Route::get('/{dailyReport}', [DailyReportController::class, 'show'])->name('show');
    Route::get('/{dailyReport}/edit', [DailyReportController::class, 'edit'])->name('edit');
    Route::put('/{dailyReport}', [DailyReportController::class, 'update'])->name('update');
    Route::delete('/{dailyReport}', [DailyReportController::class, 'destroy'])->name('destroy');
});



require __DIR__.'/auth.php';

