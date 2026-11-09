<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Logout paksa jika akun yang sedang login sudah dinonaktifkan admin lain
        $middleware->append(\App\Http\Middleware\CheckAccountStatus::class);

        $middleware->alias([
            'role'      =>  \App\Http\Middleware\CheckRole::class,
            'admin'     =>  \App\Http\Middleware\CheckRole::class . ':admin',
            'petugas'   =>  \App\Http\Middleware\CheckRole::class . ':petugas',
            'peminjam'  =>  \App\Http\Middleware\CheckRole::class . ':peminjam',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
