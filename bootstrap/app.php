<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'access.role.create' => \App\Http\Middleware\AccessRoleCreate::class,
            'access.role.create-mirror' => \App\Http\Middleware\AccessRoleCreateMirror::class,
            'access.role.mirror' => \App\Http\Middleware\AccessRoleMirror::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
