<?php

use App\Http\Middleware\EnsureAdministrator;
use App\Http\Middleware\FrameProtection;
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
        $middleware->prepend(FrameProtection::class);
        $middleware->preventRequestsDuringMaintenance(except: ['admin', 'admin/*']);
        $middleware->alias(['admin' => EnsureAdministrator::class]);
        $middleware->redirectGuestsTo(fn (): string => route('admin.login'));
        $middleware->redirectUsersTo(fn (): string => route('admin.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
