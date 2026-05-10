<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\RequesterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Requester profile page
    Route::get('/my-profile', function () {
        if (!auth()->user()->isRequester()) {
            return redirect()->route('profile.edit');
        }
        return view('profile.requester');
    })->name('profile.requester');


    // Tickets
    Route::resource('tickets', TicketController::class);
    Route::post('/tickets/{ticket}/replies', [\App\Http\Controllers\TicketReplyController::class, 'store'])
        ->name('tickets.replies.store');
    Route::delete('/tickets/{ticket}/replies/{reply}', [\App\Http\Controllers\TicketReplyController::class, 'destroy'])
        ->name('tickets.replies.destroy');

    // Admin + Supervisor + Agent
    Route::middleware(['role:admin,supervisor,support_agent'])->group(function () {
        Route::resource('requesters', RequesterController::class);
        Route::resource('categories', CategoryController::class);
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    });

    // Admin only
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
    });

    // Activity Logs — admin and supervisor only
    Route::middleware(['role:admin,supervisor'])->group(function () {
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])
            ->name('activity_logs.index');
        Route::delete('/activity-logs/clear', [ActivityLogController::class, 'clear'])
            ->name('activity_logs.clear');
    });

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',       [NotificationController::class, 'index'])->name('index');
        Route::get('/count',  [NotificationController::class, 'count'])->name('count');
    });

    Route::middleware(['role:admin,supervisor,support_agent'])->group(function () {
        Route::resource('requesters', RequesterController::class);
        Route::resource('categories', CategoryController::class);
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
        Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf'); // ✅ Add this
    });

});

require __DIR__.'/auth.php';