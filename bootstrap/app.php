<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsOfficerOrAdmin;
use App\Http\Middleware\ActivityMiddleware;
use App\Http\Middleware\CheckRole;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Daftarkan middleware untuk tracking user activity
        $middleware->append(ActivityMiddleware::class);

        $middleware->alias([
            //  Middleware LAMA (tetap dipakai)
            'isAdmin' => \App\Http\Middleware\IsAdmin::class,
            'isOfficerOrAdmin' => \App\Http\Middleware\IsOfficerOrAdmin::class,

            //  Middleware BARU (untuk registration system)
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
