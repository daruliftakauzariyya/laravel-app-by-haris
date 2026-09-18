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

        // Automatically record user logins across web, OTP, and OAuth
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Login::class, function ($event) {
            if (isset($event->user) && $event->user instanceof \App\Models\User) {
                try {
                    $event->user->recordLogin();
                    $log = \App\Models\UserLoginLog::logLogin($event->user);
                    
                    if ($event->user->hasRole('mufti') || $event->user->hasRole('admin')) {
                        \App\Models\FatwaActivityLog::create([
                            'question_id' => null,
                            'fatwa_id'    => null,
                            'user_id'     => $event->user->id,
                            'event'       => 'scholar_login',
                            'event_label' => 'Scholar ' . $event->user->name . ' Logged In from ' . ($log->device ?? 'Device'),
                            'icon'        => 'bi-box-arrow-in-right',
                            'color'       => 'success',
                            'notes'       => 'Logged in from ' . ($log->device ?? 'Device') . ' (' . ($log->platform ?: 'OS') . ' / ' . ($log->browser ?: 'Browser') . ') @ ' . now()->format('h:i A : d-m-Y') . ($log->location ? ' from ' . $log->location : ''),
                            'event_at'    => now(),
                        ]);
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Error recording login activity: ' . $e->getMessage());
                }
            }
        });

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Logout::class, function ($event) {
            if (isset($event->user) && $event->user instanceof \App\Models\User) {
                try {
                    $activeLog = \App\Models\UserLoginLog::where('user_id', $event->user->id)->where('is_active', true)->latest()->first();
                    $device = $activeLog ? $activeLog->device : 'Device';
                    $durationMins = 1;

                    if ($activeLog) {
                        $activeLog->closeSession();
                        $durationMins = max(1, round($activeLog->duration_seconds / 60));
                    }
                    
                    if ($event->user->hasRole('mufti') || $event->user->hasRole('admin')) {
                        \App\Models\FatwaActivityLog::create([
                            'question_id' => null,
                            'fatwa_id'    => null,
                            'user_id'     => $event->user->id,
                            'event'       => 'scholar_logout',
                            'event_label' => 'Scholar ' . $event->user->name . ' Logged Out from ' . $device,
                            'icon'        => 'bi-box-arrow-left',
                            'color'       => 'secondary',
                            'notes'       => 'Session ended @ ' . now()->format('h:i A : d-m-Y') . ' (Session active duration: ' . $durationMins . ' mins)',
                            'event_at'    => now(),
                        ]);
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Error recording logout activity: ' . $e->getMessage());
                }
            }
        });
    }
}
