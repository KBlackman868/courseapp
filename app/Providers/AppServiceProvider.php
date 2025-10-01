<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    public function boot(): void
    {
        // Share data with all views that use the layouts component
        View::composer('components.layouts', function ($view) {
            // Only calculate these for authenticated admin users
            if (auth()->check() && auth()->user()->hasRole(['admin', 'superadmin'])) {
                $view->with([
                    'totalCourses' => Course::count(),
                    'totalUsers' => User::count(),
                    'pendingCount' => Enrollment::where('status', 'pending')->count(),
                ]);
            }
        });
        
        Vite::prefetch(concurrency: 3);
    }
}