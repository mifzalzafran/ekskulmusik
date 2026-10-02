<?php

use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\EnsureUserIsActive;
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
        /*
         * Register route middleware aliases.
         *
         * Laravel 10 Kernel equivalent:
         * In Laravel 10, these aliases would be added to the protected $middlewareAliases array
         * inside app/Http/Kernel.php:
         *
         * protected $middlewareAliases = [
         *     'role' => \App\Http\Middleware\EnsureUserHasRole::class,
         *     'active' => \App\Http\Middleware\EnsureUserIsActive::class,
         *     'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
         * ];
         */
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'active' => EnsureUserIsActive::class,
            'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        ]);

        // Redirect unauthenticated guests to the named 'login' route
        $middleware->redirectGuestsTo(fn () => route('login'));

        // Redirect authenticated users visiting guest-only routes (e.g. /login) by their role
        $middleware->redirectUsersTo(fn (Request $request) => $request->user()?->isAdmin()
            ? route('admin.dashboard')
            : route('member.dashboard')
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
