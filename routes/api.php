<?php

use App\Http\Controllers\Api\Admin\ProjectManagementController as AdminProjectManagementController;
use App\Http\Controllers\Api\Admin\ProjectUpdateController as AdminProjectUpdateController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ProjectMessageController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/projects', [ProjectController::class, 'index'])->name('api.projects.index');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('api.projects.show');
    Route::post('/projects/{project}/messages', [ProjectMessageController::class, 'store'])->name('api.projects.messages.store');

    Route::middleware('can:manage-projects')
        ->prefix('admin')
        ->name('api.admin.')
        ->group(function (): void {
            Route::get('/projects', [AdminProjectManagementController::class, 'index'])->name('projects.index');
            Route::patch('/projects/{project}/status', [AdminProjectManagementController::class, 'updateStatus'])->name('projects.update-status');
            Route::post('/projects/{project}/updates', [AdminProjectUpdateController::class, 'store'])->name('projects.updates.store');
        });
});
