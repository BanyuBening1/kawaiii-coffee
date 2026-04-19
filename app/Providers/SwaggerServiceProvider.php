<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SwaggerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Mencegah warning swagger-php dikonversi menjadi ErrorException oleh Laravel
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            if (
                str_contains((string) $errfile, 'swagger-php') ||
                str_contains((string) $errfile, 'zircote')
            ) {
                return true; // abaikan warning dari swagger-php
            }
            return false; // error lain tetap diproses normal
        }, E_USER_WARNING | E_USER_NOTICE);
    }
}