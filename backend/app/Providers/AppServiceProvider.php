<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production' || request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }

        $this->configureRateLimiting();
    }

    /**
     * Rate limiters for the public REST API.
     */
    protected function configureRateLimiting(): void
    {
        // General read limit for every /api request.
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(120)->by($request->ip()));

        // Comment submissions: a short burst limit plus an hourly cap per IP.
        RateLimiter::for('comments', fn (Request $request) => [
            Limit::perMinute(5)->by('comments-minute:'.$request->ip()),
            Limit::perHour(30)->by('comments-hour:'.$request->ip()),
        ]);
    }
}