<?php

use App\Http\Middleware\EnsureRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias middleware: dipanggil sebagai 'role:admin' di routes/web.php
        $middleware->alias([
            'role' => EnsureRole::class,
        ]);

        // Pengunjung yang belum login diarahkan ke halaman login.
        $middleware->redirectGuestsTo(fn (Request $request) => route('login'));

        // Pengguna yang sudah login tapi membuka /login diarahkan ke beranda sesuai role.
        $middleware->redirectUsersTo(fn (Request $request) => $request->user()?->isAdmin()
            ? route('admin.dashboard')
            : route('absensi'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
