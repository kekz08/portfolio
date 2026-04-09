<?php

use App\Http\Controllers\Api\Admin\ProjectManagementController as AdminProjectManagementController;
use App\Http\Controllers\Api\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/projects', [ProjectController::class, 'index'])->name('api.projects.index');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('api.projects.show');

    Route::middleware('can:manage-projects')
        ->prefix('admin')
        ->name('api.admin.')
        ->group(function (): void {
            Route::get('/projects', [AdminProjectManagementController::class, 'index'])->name('projects.index');
            Route::patch('/projects/{project}/status', [AdminProjectManagementController::class, 'updateStatus'])->name('projects.update-status');
        });
});
