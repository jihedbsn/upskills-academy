<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Envoyer les emails de tâches planifiées toutes les minutes
        $schedule->command('tasks:send-emails')
            ->everyMinute()
            ->name('send-scheduled-task-emails')
            ->description('Send scheduled task reminder emails to students every minute');

        // Generate and send automatic certificates every minute
        $schedule->command('certificates:generate-automatic')
            ->everyMinute()
            ->name('generate-automatic-certificates')
            ->description('Generate and send automatic certificates for validated student successes')
            ->onSuccess(function () {
                Log::info('Automatic certificate generation completed successfully');
            })
            ->onFailure(function () {
                Log::error('Automatic certificate generation failed');
            });

        // Send birthday greetings daily at 8 AM
        $schedule->command('birthday:send-greetings')
            ->dailyAt('08:00')
            ->name('send-birthday-greetings')
            ->description('Send birthday greeting emails to students whose birthday is today')
            ->onSuccess(function () {
                Log::info('Birthday greetings sent successfully');
            })
            ->onFailure(function () {
                Log::error('Birthday greetings sending failed');
            });

        // Track abandoned carts and send reminders every hour
        $schedule->command('carts:track-abandoned')
            ->hourly()
            ->name('track-abandoned-carts')
            ->description('Track abandoned carts and send reminder emails')
            ->onSuccess(function () {
                Log::info('Abandoned cart tracking completed successfully');
            })
            ->onFailure(function () {
                Log::error('Abandoned cart tracking failed');
            });

        // Process overdue installments and apply daily late fees at midnight
        $schedule->command('installments:process-overdue')
            ->dailyAt('00:00')
            ->name('process-overdue-installments')
            ->description('Process overdue installments, suspend accounts, and apply $5/day late fees')
            ->onSuccess(function () {
                Log::info('Overdue installment processing completed successfully');
            })
            ->onFailure(function () {
                Log::error('Overdue installment processing failed');
            });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
