<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ================================================
// SCHEDULED TASKS CONFIGURATION
// ================================================

// Schedule: Check for course expirations and send reminders daily at 6 AM
Schedule::command('courses:check-expirations')
    ->daily()
    ->at('06:00')
    ->name('check-course-expirations')
    ->description('Check for expired courses and send expiration reminders')
    ->onSuccess(function () {
        Log::info('Course expiration check completed successfully');
    })
    ->onFailure(function () {
        Log::error('Course expiration check failed');
    });

// Schedule: Send evaluation reminders weekly on Monday at 8 AM
Schedule::command('evaluations:send-reminders')
    ->weekly()
    ->mondays()
    ->at('08:00')
    ->name('send-evaluation-reminders')
    ->description('Send evaluation reminder emails to students')
    ->onSuccess(function () {
        Log::info('Evaluation reminders sent successfully');
    })
    ->onFailure(function () {
        Log::error('Evaluation reminders failed');
    });

// Schedule: Check certificates data daily at 2 AM
Schedule::command('check:certificates-data')
    ->daily()
    ->at('02:00')
    ->name('check-certificates-data')
    ->description('Check and validate certificates data')
    ->onSuccess(function () {
        Log::info('Certificates data check completed successfully');
    })
    ->onFailure(function () {
        Log::error('Certificates data check failed');
    });

// Schedule: Process overdue installments and daily penalties at midnight
Schedule::command('installments:process-overdue')
    ->daily()
    ->at('00:00')
    ->name('process-overdue-installments')
    ->description('Suspend overdue installment accounts and apply daily $5 penalties')
    ->onSuccess(function () {
        Log::info('Overdue installment processing completed successfully');
    })
    ->onFailure(function () {
        Log::error('Overdue installment processing failed');
    });

// Schedule: Send pre-due installment reminders daily at 09:00
Schedule::command('installments:send-due-reminders')
    ->daily()
    ->at('09:00')
    ->name('send-installment-due-reminders')
    ->description('Send installment reminders at 20/10/5/1 days before due date')
    ->onSuccess(function () {
        Log::info('Installment due reminders sent successfully');
    })
    ->onFailure(function () {
        Log::error('Installment due reminders failed');
    });
