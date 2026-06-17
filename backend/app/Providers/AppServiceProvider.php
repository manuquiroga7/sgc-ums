<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
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
        // El motor por defecto de MySQL en este WAMP limita las claves a 1000 bytes;
        // con utf8mb4 un varchar(255) único excede ese límite. Fijamos 191 como
        // longitud por defecto para los índices de strings.
        Schema::defaultStringLength(191);
    }
}
