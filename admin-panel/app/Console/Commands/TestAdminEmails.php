<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Student;
use App\Models\Product;
use App\Mail\StudentAccountCreated;
use App\Mail\BirthdayGreeting;
use App\Mail\CertificateReady;
use App\Mail\QuizPassed;
use App\Mail\StageValidated;
use App\Mail\StageRejected;
use App\Mail\StudentSuccessApproved;
use App\Mail\StudentSuccessRejected;
use App\Mail\VideoExamValidated;
use App\Mail\VideoExamRejected;
use App\Mail\AdminNewPurchase;
use App\Mail\ScheduledTask;

class TestAdminEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:test-emails
                            {email : The email address to send test emails to}
                            {--template= : Test only a specific template (student-account|birthday|certificate|quiz|stage-validated|stage-rejected|success-approved|success-rejected|video-validated|video-rejected|purchase|scheduled)}
                            {--delay=2 : Delay in seconds between each email}
                            {--no-delay : Skip delays between emails}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test all admin email templates with comprehensive reporting and error handling';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetEmail = $this->argument('email');
        $specificTemplate = $this->option('template');
        $delay = $this->option('no-delay') ? 0 : (int)$this->option('delay');

        $this->info('╔══════════════════════════════════════════════════════════╗');
        $this->info('║       Upskills Academy - Admin Email Test Suite          ║');
        $this->info('╚══════════════════════════════════════════════════════════╝');
        $this->newLine();

        // Prepare test data
        $this->line('📦 Preparing test data...');

        $student = $this->getTestStudent();
        $course = $this->getTestCourse();
        $product = $this->getTestProduct();

        $this->info('✓ Test data ready');
        $this->newLine();

        // Define all email tests
        $emailTests = [
            'student-account' => [
                'name' => 'Student Account Created',
                'icon' => '👤',
                'callable' => fn() => new StudentAccountCreated($student, $product, 'TempPass123'),
            ],
            'birthday' => [
                'name' => 'Birthday Greeting',
                'icon' => '🎂',
                'callable' => fn() => new BirthdayGreeting($student),
            ],
            'certificate' => [
                'name' => 'Certificate Ready',
                'icon' => '📜',
                'callable' => fn() => new CertificateReady($student, $course, 'CERT-2024-001234', 95, 'https://example.com/cert'),
            ],
            'quiz' => [
                'name' => 'Quiz Passed',
                'icon' => '✅',
                'callable' => fn() => new QuizPassed($student, 'Advanced Techniques Final', 'Final Exam', 92, 'Outstanding performance with excellent technique demonstration.'),
            ],
            'stage-validated' => [
                'name' => 'Stage Validated',
                'icon' => '✔️',
                'callable' => fn() => new StageValidated($student, 'Practical Training Module', now()->format('F d, Y'), 'Proceed to certification exam.'),
            ],
            'stage-rejected' => [
                'name' => 'Stage Rejected',
                'icon' => '❌',
                'callable' => fn() => new StageRejected($student, 'Practical Training Module', now()->format('F d, Y'), 'Additional practice required in form techniques.', 'Complete the supplementary exercises and resubmit.'),
            ],
            'success-approved' => [
                'name' => 'Student Success Approved',
                'icon' => '🎓',
                'callable' => fn() => new StudentSuccessApproved($student, $course, now()->format('F d, Y')),
            ],
            'success-rejected' => [
                'name' => 'Student Success Rejected',
                'icon' => '📋',
                'callable' => fn() => new StudentSuccessRejected($student, $course, now()->format('F d, Y'), 'Additional documentation required to validate completion.'),
            ],
            'video-validated' => [
                'name' => 'Video Exam Validated',
                'icon' => '🎥',
                'callable' => fn() => new VideoExamValidated($student, 'Practical Demonstration Video', now()->format('F d, Y'), 'Excellent form and technique demonstrated throughout.'),
            ],
            'video-rejected' => [
                'name' => 'Video Exam Rejected',
                'icon' => '📹',
                'callable' => fn() => new VideoExamRejected($student, 'Practical Demonstration Video', now()->format('F d, Y'), 'Video quality insufficient for proper evaluation.', 'Re-record in better lighting with clear camera angle.'),
            ],
            'purchase' => [
                'name' => 'Admin New Purchase',
                'icon' => '💳',
                'callable' => fn() => new AdminNewPurchase(
                    [
                        'order_id' => 'ORD-' . now()->format('Ymd') . '-' . rand(1000, 9999),
                        'order_date' => now()->format('F d, Y'),
                        'student_name' => $student->name ?? 'John Doe',
                        'student_email' => $student->email ?? 'test@example.com',
                        'total_amount' => '2,499.00',
                        'currency' => 'SEK',
                        'payment_status' => 'Completed'
                    ],
                    (object)['phone' => '+46701234567', 'country' => 'Sweden'],
                    (object)['id' => rand(10000, 99999)]
                ),
            ],
            'scheduled' => [
                'name' => 'Scheduled Task Reminder',
                'icon' => '⏰',
                'callable' => fn() => new ScheduledTaskReminder($student, 'Complete Chapter 5 Assessment', now()->addDay()->format('F d, Y'), '14:00', 'tomorrow', 'Early completion bonus: 10% off next course!'),
            ],
        ];

        // Filter to specific template if requested
        if ($specificTemplate) {
            if (!isset($emailTests[$specificTemplate])) {
                $this->error("❌ Template '{$specificTemplate}' not found!");
                $this->line('Available templates: ' . implode(', ', array_keys($emailTests)));
                return 1;
            }
            $emailTests = [$specificTemplate => $emailTests[$specificTemplate]];
        }

        // Send emails and track results
        $results = [
            'success' => [],
            'failed' => [],
            'total' => count($emailTests),
            'start_time' => microtime(true),
        ];

        $this->info("📧 Sending " . $results['total'] . " email(s) to: {$targetEmail}");
        $this->newLine();

        $bar = $this->output->createProgressBar($results['total']);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');

        foreach ($emailTests as $key => $test) {
            $bar->setMessage("Sending: {$test['name']}");

            try {
                $startTime = microtime(true);

                Mail::to($targetEmail)->send($test['callable']());

                $duration = round((microtime(true) - $startTime) * 1000, 2);

                $results['success'][] = [
                    'name' => $test['name'],
                    'icon' => $test['icon'],
                    'duration' => $duration,
                ];

                $bar->advance();

                if ($delay > 0 && $key !== array_key_last($emailTests)) {
                    sleep($delay);
                }

            } catch (\Exception $e) {
                $results['failed'][] = [
                    'name' => $test['name'],
                    'icon' => $test['icon'],
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ];
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);

        // Display results
        $totalDuration = round(microtime(true) - $results['start_time'], 2);

        $this->info('╔══════════════════════════════════════════════════════════╗');
        $this->info('║                    Test Results                          ║');
        $this->info('╚══════════════════════════════════════════════════════════╝');
        $this->newLine();

        if (count($results['success']) > 0) {
            $this->info('✅ Successful Emails (' . count($results['success']) . '):');
            $this->newLine();

            foreach ($results['success'] as $success) {
                $this->line(sprintf(
                    '  %s %s <fg=green>✓</> <fg=gray>(%s ms)</>',
                    $success['icon'],
                    $success['name'],
                    $success['duration']
                ));
            }
            $this->newLine();
        }

        if (count($results['failed']) > 0) {
            $this->error('❌ Failed Emails (' . count($results['failed']) . '):');
            $this->newLine();

            foreach ($results['failed'] as $failure) {
                $this->line(sprintf(
                    '  %s %s <fg=red>✗</>',
                    $failure['icon'],
                    $failure['name']
                ));
                $this->line('     <fg=red>Error:</> ' . $failure['error']);

                if ($this->output->isVerbose()) {
                    $this->line('     <fg=gray>Trace:</>');
                    $this->line('     ' . str_replace("\n", "\n     ", substr($failure['trace'], 0, 500)));
                }
                $this->newLine();
            }
        }

        // Summary statistics
        $this->info('╔══════════════════════════════════════════════════════════╗');
        $this->info('║                      Summary                             ║');
        $this->info('╚══════════════════════════════════════════════════════════╝');
        $this->newLine();

        $successRate = round((count($results['success']) / $results['total']) * 100, 1);

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Emails', $results['total']],
                ['Successful', '<fg=green>' . count($results['success']) . '</>'],
                ['Failed', count($results['failed']) > 0 ? '<fg=red>' . count($results['failed']) . '</>' : '0'],
                ['Success Rate', $successRate . '%'],
                ['Total Duration', $totalDuration . 's'],
                ['Average Time', round($totalDuration / $results['total'], 2) . 's per email'],
                ['Target Email', $targetEmail],
            ]
        );

        $this->newLine();

        if (count($results['failed']) === 0) {
            $this->info('🎉 All emails sent successfully!');
            $this->line('📬 Check your inbox at: ' . $targetEmail);
            return 0;
        } else {
            $this->warn('⚠️  Some emails failed to send. Run with -v flag for detailed error traces.');
            return 1;
        }
    }

    /**
     * Get a test student instance
     */
    private function getTestStudent()
    {
        $student = Student::first();

        if (!$student) {
            $this->warn('⚠️  No students in database, creating factory instance');
            $student = Student::factory()->make([
                'name' => 'John Anderson',
                'email' => 'john.anderson@example.com',
                'phone' => '+46701234567',
                'country' => 'Sweden',
            ]);
        }

        return $student;
    }

    /**
     * Get a test course instance
     */
    private function getTestCourse()
    {
        // Create mock course object
        $course = (object)[
            'name' => 'IELTS Exam Preparation',
            'id' => 1,
        ];

        return $course;
    }

    /**
     * Get a test product instance
     */
    private function getTestProduct()
    {
        $product = Product::first();

        if (!$product) {
            $this->warn('⚠️  No products in database, creating mock instance');
            $product = (object)[
                'name' => 'Premium Course Bundle',
                'id' => 1,
            ];
        }

        return $product;
    }
}
