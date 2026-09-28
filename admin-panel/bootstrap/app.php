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
    ->withSchedule(function ($schedule) {
        // Envoyer les emails de tâches planifiées toutes les minutes
        $schedule->command('tasks:send-emails')
            ->everyMinute()
            ->name('send-scheduled-task-emails')
            ->description('Send scheduled task reminder emails to students every minute');

        $schedule->command('emails:fetch-inbound')
            ->everyFiveMinutes()
            ->when(fn () => config('email-inbox.imap.enabled'))
            ->name('fetch-inbound-emails')
            ->description('Import inbound emails from IMAP inbox');
    })
    ->withMiddleware(function (Middleware $middleware): void {
        // Enregistrer les alias de middleware
        $middleware->alias([
            'en.locale' => \App\Http\Middleware\SetEnglishLocale::class,
            'admin.auth' => \App\Http\Middleware\AdminAuth::class,
        ]);

        // Appliquer le middleware auth à toutes les routes web sauf login
        $middleware->web(append: [
            \App\Http\Middleware\AdminAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
