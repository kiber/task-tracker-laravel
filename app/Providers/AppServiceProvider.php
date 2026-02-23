<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        $loginRateLimitResponse = function(Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Too many login attempts. Please try again in a few minutes.',
                ], 429);
            }

            return back()
                ->withInput($request->except('password'))
                ->withErrors([
                    'email' => 'Too many login attempts. Please try again in a few minutes.'
                ]);
        };

        // middleware throttle:login
        RateLimiter::for('login', function (Request $request) use ($loginRateLimitResponse) {
            return [
                Limit::perMinute(100)->by($request->ip())->response($loginRateLimitResponse),
                Limit::perMinute(3)->by($request->input('email'))->response($loginRateLimitResponse),
            ];
        });
    }
}
