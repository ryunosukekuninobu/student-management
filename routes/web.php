<?php

use Calema\StudentManagement\Http\Controllers\StudentController;
use Calema\StudentManagement\Http\Controllers\StudentInvitationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    // Student Routes
    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('index');
        Route::get('/create', [StudentController::class, 'create'])->name('create');
        Route::post('/', [StudentController::class, 'store'])->name('store');
        Route::get('/{student}', [StudentController::class, 'show'])->name('show');
        Route::get('/{student}/edit', [StudentController::class, 'edit'])->name('edit');
        Route::put('/{student}', [StudentController::class, 'update'])->name('update');
        Route::delete('/{student}', [StudentController::class, 'destroy'])->name('destroy');

        // Invitation Routes
        Route::get('/invite/create', [StudentInvitationController::class, 'create'])->name('invite');
        Route::post('/invite', [StudentInvitationController::class, 'store'])->name('invite.send');
    });

    // Public invitation acceptance (no auth required for these)
    Route::get('/invitation/{token}', [StudentInvitationController::class, 'showAcceptForm'])->name('students.invitation.show')->withoutMiddleware('auth');
    Route::post('/invitation/{token}/accept', [StudentInvitationController::class, 'accept'])->name('students.invitation.accept')->withoutMiddleware('auth');
});
