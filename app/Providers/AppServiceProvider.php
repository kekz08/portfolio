<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\User;
use App\Policies\ProjectPolicy;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Eloquent\EloquentProjectRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProjectRepositoryInterface::class, EloquentProjectRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::define('manage-projects', fn (User $user): bool => $user->isAdmin());
    }
}
