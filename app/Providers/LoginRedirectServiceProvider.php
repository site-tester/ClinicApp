<?php
namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class LoginRedirectServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Listen for Backpack login events
        // Event::listen('Illuminate\Auth\Events\Login', function ($event) {
        //     $user = $event->user;

        //     // If user has Patient role, store intended redirect
        //     // if ($user->hasRole('Patient')) {
        //     //     session(['intended_url' => route('patient.dashboard')]);
        //     // }

        //     if ($user && $user->hasRole('Patient')) {
        //         // store Laravel's standard intended key (so redirect()->intended() works)
        //         session(['url.intended' => route('patient.dashboard')]);
        //     }
        // });

        // Override Backpack's redirect after login
        // $this->app->resolving('Backpack\CRUD\app\Http\Controllers\Auth\LoginController', function ($controller) {
        //     $controller->redirectTo = function () {
        //         if (auth()->user()->hasRole('Patient')) {
        //             return route('patient.dashboard');
        //         }
        //         return config('backpack.base.route_prefix', 'admin') . '/dashboard';
        //     };
        // });

        // $this->app->bind('Backpack\CRUD\app\Http\Controllers\Auth\LoginController', function ($app) {
        //     $controller = new \App\Http\Controllers\Auth\LoginController();

        //     $controller->redirectTo = function () {
        //         if (auth()->user()->hasRole('Patient')) {
        //             return route('patient.dashboard');
        //         }
        //         return config('backpack.base.route_prefix', 'admin') . '/dashboard';
        //     };

        //     return $controller;
        // });
    }
}
