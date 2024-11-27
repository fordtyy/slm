<?php

namespace App\Providers;

use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Http\Responses\Auth\Contracts\EmailVerificationResponse;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            LoginResponse::class,
            \App\Http\Responses\LoginResponse::class
        );
        $this->app->singleton(
            RegistrationResponse::class,
            \App\Http\Responses\RegistrationResponse::class
        );
        $this->app->singleton(
          EmailVerificationResponse::class, 
          \App\Http\Responses\EmailVerificationResponse::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
