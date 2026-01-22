<?php

use Calema\StudentManagement\Http\Controllers\StudentController;
use Calema\StudentManagement\Http\Controllers\StudentInvitationController;
use Calema\StudentManagement\Http\Controllers\TenantSettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    // Student Routes
    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('index');

        // Specific routes must come before parameterized routes
        Route::get('/create', [StudentController::class, 'create'])->name('create');

        // Invitation Routes
        Route::get('/invite/create', [StudentInvitationController::class, 'create'])->name('invite');
        Route::post('/invite', [StudentInvitationController::class, 'store'])->name('invite.send');

        // Settings Routes (must come before /{student})
        Route::get('/settings', [TenantSettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [TenantSettingsController::class, 'update'])->name('settings.update');

        // Custom Fields Routes
        Route::post('/settings/custom-fields', [TenantSettingsController::class, 'storeCustomField'])->name('settings.custom-fields.store');
        Route::put('/settings/custom-fields/{customField}', [TenantSettingsController::class, 'updateCustomField'])->name('settings.custom-fields.update');
        Route::delete('/settings/custom-fields/{customField}', [TenantSettingsController::class, 'destroyCustomField'])->name('settings.custom-fields.destroy');

        // Parameterized routes must come last
        Route::post('/', [StudentController::class, 'store'])->name('store');
        Route::get('/{student}', [StudentController::class, 'show'])->name('show');
        Route::get('/{student}/edit', [StudentController::class, 'edit'])->name('edit');
        Route::put('/{student}', [StudentController::class, 'update'])->name('update');
        Route::delete('/{student}', [StudentController::class, 'destroy'])->name('destroy');
    });

    // Public invitation acceptance (no auth required for these)
    Route::get('/invitation/{token}', [StudentInvitationController::class, 'showAcceptForm'])->name('students.invitation.show')->withoutMiddleware('auth');
    Route::post('/invitation/{token}/accept', [StudentInvitationController::class, 'accept'])->name('students.invitation.accept')->withoutMiddleware('auth');
});
