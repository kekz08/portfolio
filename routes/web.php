<?php

use App\Http\Controllers\Admin\HomeContentController;
use App\Http\Controllers\Admin\ProjectManagementController;
use App\Http\Controllers\Admin\ProjectUpdateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ProjectMessageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::controller(PortfolioController::class)->group(function (): void {
    Route::get('/', 'index')->name('home');
    Route::get('/blogs', 'blogs')->name('blogs');
    Route::get('/blog-details', 'blogDetails')->name('blog-details');
});

Route::get('/hire-me', fn () => redirect()->route('register'))->name('hire-me');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/projects/{project}/messages', [ProjectMessageController::class, 'store'])->name('projects.messages.store');

    Route::middleware('can:manage-projects')
        ->prefix('admin')
        ->name('admin.')
        ->group(function (): void {
            Route::get('/home-content', [HomeContentController::class, 'edit'])->name('home-content.edit');
            Route::patch('/home-content', [HomeContentController::class, 'update'])->name('home-content.update');
            Route::post('/home-content/reset', [HomeContentController::class, 'reset'])->name('home-content.reset');
            Route::get('/projects', [ProjectManagementController::class, 'index'])->name('projects.index');
            Route::patch('/projects/{project}/status', [ProjectManagementController::class, 'update'])->name('projects.update-status');
            Route::post('/projects/{project}/updates', [ProjectUpdateController::class, 'store'])->name('projects.updates.store');
        });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
