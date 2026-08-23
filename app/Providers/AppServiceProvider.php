<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        RateLimiter::for('api-general', function (Request $request) {
            $key = $request->user()
                ? 'user:'.$request->user()->getAuthIdentifier()
                : 'ip:'.$request->ip();

            return Limit::perMinute(60)
                ->by($key)
                ->response(fn (Request $request, array $headers) => response()->json([
                    'message' => 'Too Many Requests.',
                ], 429, $headers));
        });

        RateLimiter::for('api-login', function (Request $request) {
            $key = hash('sha256', Str::lower((string) $request->input('email')).'|'.$request->ip());

            return Limit::perMinute(5)
                ->by($key)
                ->response(fn (Request $request, array $headers) => response()->json([
                    'message' => 'Too Many Requests.',
                ], 429, $headers));
        });
    }
}
