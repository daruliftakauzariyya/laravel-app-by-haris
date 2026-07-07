<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Centralized Cache Invalidation closure
        $clearCache = function () {
            \Illuminate\Support\Facades\Cache::flush();
        };

        // Attach listeners to relevant models
        \App\Models\QuestionAndAnswer::saved($clearCache);
        \App\Models\QuestionAndAnswer::deleted($clearCache);
        \App\Models\Category::saved($clearCache);
        \App\Models\Category::deleted($clearCache);
    }
}
