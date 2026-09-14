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

        // Global Blade Directives for Status Management
        \Illuminate\Support\Facades\Blade::directive('statusBadge', function ($expression) {
            return "<?php echo \App\Models\Status::renderBadge($expression); ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('statusLabel', function ($expression) {
            return "<?php echo \App\Models\Status::getLabel($expression); ?>";
        });
    }
}
