<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Mail\BirthdayGreeting;
use App\Services\OutboundEmailLogger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendBirthdayGreetings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birthday:send-greetings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send birthday greeting emails to students whose birthday is today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        // Get all students whose birthday is today (month and day match)
        $birthdayStudents = Student::whereNotNull('birthdate')
            ->whereMonth('birthdate', $today->month)
            ->whereDay('birthdate', $today->day)
            ->get();

        if ($birthdayStudents->isEmpty()) {
            $this->info('No birthdays today.');
            Log::info('Birthday greetings: No birthdays today.');
            return 0;
        }

        $sentCount = 0;
        $failedCount = 0;

        foreach ($birthdayStudents as $student) {
            try {
                // Send birthday greeting email with random card variant
                Mail::to($student->email)->send(new BirthdayGreeting($student));

                OutboundEmailLogger::logSent(
                    $student->email,
                    'birthday_greeting',
                    'Happy Birthday from Upskills Academy!',
                    $student->id,
                    $student->name
                );

                $sentCount++;
                $this->info("Birthday greeting sent to: {$student->name} ({$student->email})");

                Log::info("Birthday greeting sent successfully", [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'student_email' => $student->email
                ]);

            } catch (\Exception $e) {
                $failedCount++;

                OutboundEmailLogger::logFailed(
                    $student->email,
                    'birthday_greeting',
                    'Happy Birthday from Upskills Academy!',
                    $e->getMessage(),
                    $student->id,
                    $student->name
                );

                $this->error("Failed to send birthday greeting to: {$student->name} ({$student->email})");

                Log::error("Failed to send birthday greeting", [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'student_email' => $student->email,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("Birthday greetings summary: {$sentCount} sent, {$failedCount} failed.");
        Log::info("Birthday greetings completed", [
            'total_birthdays' => $birthdayStudents->count(),
            'sent' => $sentCount,
            'failed' => $failedCount
        ]);

        return 0;
    }
}
