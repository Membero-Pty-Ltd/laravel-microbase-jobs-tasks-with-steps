<?php

use App\Http\Middleware\AccessRoleCreate;
use App\Http\Middleware\AccessRoleCreateMirror;
use App\Http\Middleware\AccessRoleMirror;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'access.role.create' => AccessRoleCreate::class,
            'access.role.create-mirror' => AccessRoleCreateMirror::class,
            'access.role.mirror' => AccessRoleMirror::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
