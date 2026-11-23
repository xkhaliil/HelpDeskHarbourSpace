<?php

use App\Http\Middleware\IsAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Lets us write ->middleware('admin') on the admin routes.
        $middleware->alias([
            'admin' => IsAdmin::class,
        ]);

        // On Vercel every request passes through Vercel's proxy, which handles HTTPS.
        // Trusting it makes Laravel generate https:// links for the CSS, JS and routes.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
