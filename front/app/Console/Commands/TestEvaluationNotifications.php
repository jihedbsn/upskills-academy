<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\Product;
use App\Models\StudentSuccess;
use App\Models\CourseRating;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestEvaluationNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:evaluation-notifications
                            {--student-email= : Email of the test student (default: mahdi@gmail.com)}
                            {--scenario= : Specific scenario to test (1-7)}
                            {--cleanup : Clean up test data after running}
                            {--send-email : Send evaluation reminder email after setup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the evaluation notification system with various scenarios';

    private $testStudent;
    private $testProducts = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Evaluation Notification System Test');
        $this->info('=====================================');
        $this->newLine();

        // Get or create test student
        $email = $this->option('student-email') ?? 'mahdi@gmail.com';
        $this->testStudent = $this->getOrCreateTestStudent($email);

        if (!$this->testStudent) {
            $this->error('Failed to create/find test student');
            return 1;
        }

        $this->info("✅ Using test student: {$this->testStudent->email} (ID: {$this->testStudent->id})");
        $this->newLine();

        // Get test products
        $this->testProducts = Product::limit(5)->get();
        if ($this->testProducts->count() < 3) {
            $this->error('Not enough products in database. Need at least 3 products.');
            return 1;
        }

        $this->info("✅ Found {$this->testProducts->count()} products for testing");
        $this->newLine();

        // Run specific scenario or all scenarios
        $scenario = $this->option('scenario');

        if ($scenario) {
            $this->runScenario((int)$scenario);
        } else {
            $this->runAllScenarios();
        }

        // Cleanup if requested
        if ($this->option('cleanup')) {
            $this->cleanup();
        }

        // Send evaluation reminder email if requested
        if ($this->option('send-email')) {
            $this->sendEvaluationEmail();
        }

        $this->newLine();
        $this->info('✅ Testing complete!');
        $this->info("🌐 Login URL: " . url('/login'));
        $this->info("📧 Email: {$this->testStudent->email}");
        $this->info("🔑 Password: password");

        return 0;
    }

    /**
     * Send evaluation reminder email
     */
    private function sendEvaluationEmail()
    {
        $this->newLine();
        $this->info('📧 Sending Evaluation Reminder Email...');

        try {
            $this->call('evaluations:send-reminders', [
                '--student-email' => $this->testStudent->email,
            ]);

            $this->info('✓ Email sent successfully!');
            $this->info("Check inbox: {$this->testStudent->email}");
        } catch (\Exception $e) {
            $this->error("✗ Failed to send email: {$e->getMessage()}");
        }
    }

    /**
     * Run all test scenarios
     */
    private function runAllScenarios()
    {
        $this->info('Running all test scenarios...');
        $this->newLine();

        for ($i = 1; $i <= 7; $i++) {
            $this->runScenario($i);
            $this->newLine();

            if ($i < 7) {
                $this->info('Press Enter to continue to next scenario...');
                fgets(STDIN);
                $this->cleanup();
            }
        }
    }

    /**
     * Run a specific test scenario
     */
    private function runScenario(int $scenario)
    {
        $this->info("📋 Test Scenario {$scenario}");
        $this->line(str_repeat('-', 50));

        switch ($scenario) {
            case 1:
                $this->testCase1_NoPendingEvaluations();
                break;
            case 2:
                $this->testCase2_OnePendingEvaluation();
                break;
            case 3:
                $this->testCase3_MultiplePendingEvaluations();
                break;
            case 4:
                $this->testCase4_PartialEvaluations();
                break;
            case 5:
                $this->testCase5_CompleteAllEvaluations();
                break;
            case 6:
                $this->testCase6_RejectedCourse();
                break;
            case 7:
                $this->testCase7_PendingCourse();
                break;
            default:
                $this->error("Invalid scenario number: {$scenario}");
                return;
        }

        $this->displayResults();
    }

    /**
     * Test Case 1: No Pending Evaluations
     */
    private function testCase1_NoPendingEvaluations()
    {
        $this->info('Scenario: No completed courses or all courses already rated');

        // Clean slate - no data
        $this->cleanup();

        $this->line('✓ No student_successes records created');
        $this->line('✓ No course_ratings records created');
    }

    /**
     * Test Case 2: One Pending Evaluation
     */
    private function testCase2_OnePendingEvaluation()
    {
        $this->info('Scenario: One completed course without rating');

        $this->cleanup();

        $product = $this->testProducts->first();

        StudentSuccess::create([
            'product_id' => $product->id,
            'student_id' => $this->testStudent->id,
            'success' => 1,
            'submitted_at' => now(),
            'validated_at' => now(),
        ]);

        $this->line("✓ Created success record for: {$product->titre}");
        $this->line('✓ No rating created');
    }

    /**
     * Test Case 3: Multiple Pending Evaluations
     */
    private function testCase3_MultiplePendingEvaluations()
    {
        $this->info('Scenario: Three completed courses without ratings');

        $this->cleanup();

        $products = $this->testProducts->take(3);

        foreach ($products as $product) {
            StudentSuccess::create([
                'product_id' => $product->id,
                'student_id' => $this->testStudent->id,
                'success' => 1,
                'submitted_at' => now(),
                'validated_at' => now(),
            ]);

            $this->line("✓ Created success record for: {$product->titre}");
        }

        $this->line('✓ No ratings created');
    }

    /**
     * Test Case 4: Partial Evaluations
     */
    private function testCase4_PartialEvaluations()
    {
        $this->info('Scenario: Three completed courses, one rated');

        $this->cleanup();

        $products = $this->testProducts->take(3);

        foreach ($products as $index => $product) {
            StudentSuccess::create([
                'product_id' => $product->id,
                'student_id' => $this->testStudent->id,
                'success' => 1,
                'submitted_at' => now(),
                'validated_at' => now(),
            ]);

            $this->line("✓ Created success record for: {$product->titre}");

            // Rate only the first course
            if ($index === 0) {
                CourseRating::create([
                    'product_id' => $product->id,
                    'student_id' => $this->testStudent->id,
                    'rating' => 5,
                    'commentaire' => 'Great course! (Test data)',
                    'is_approved' => true,
                    'approved_at' => now(),
                ]);

                $this->line("  → Rated with 5 stars");
            }
        }
    }

    /**
     * Test Case 5: Complete All Evaluations
     */
    private function testCase5_CompleteAllEvaluations()
    {
        $this->info('Scenario: Three completed courses, all rated');

        $this->cleanup();

        $products = $this->testProducts->take(3);

        foreach ($products as $product) {
            StudentSuccess::create([
                'product_id' => $product->id,
                'student_id' => $this->testStudent->id,
                'success' => 1,
                'submitted_at' => now(),
                'validated_at' => now(),
            ]);

            CourseRating::create([
                'product_id' => $product->id,
                'student_id' => $this->testStudent->id,
                'rating' => rand(4, 5),
                'commentaire' => 'Test rating',
                'is_approved' => true,
                'approved_at' => now(),
            ]);

            $this->line("✓ Created success + rating for: {$product->titre}");
        }
    }

    /**
     * Test Case 6: Rejected Course
     */
    private function testCase6_RejectedCourse()
    {
        $this->info('Scenario: One rejected course submission');

        $this->cleanup();

        $product = $this->testProducts->first();

        StudentSuccess::create([
            'product_id' => $product->id,
            'student_id' => $this->testStudent->id,
            'success' => -1, // Rejected
            'submitted_at' => now(),
            'validated_at' => now(),
            'admin_notes' => 'Test rejection',
        ]);

        $this->line("✓ Created REJECTED success record for: {$product->titre}");
        $this->line('✓ No rating created (rejected courses don\'t require evaluation)');
    }

    /**
     * Test Case 7: Pending Course
     */
    private function testCase7_PendingCourse()
    {
        $this->info('Scenario: One pending course submission');

        $this->cleanup();

        $product = $this->testProducts->first();

        StudentSuccess::create([
            'product_id' => $product->id,
            'student_id' => $this->testStudent->id,
            'success' => 0, // Pending
            'submitted_at' => now(),
        ]);

        $this->line("✓ Created PENDING success record for: {$product->titre}");
        $this->line('✓ No rating created (pending courses don\'t require evaluation yet)');
    }

    /**
     * Display current results
     */
    private function displayResults()
    {
        $this->newLine();
        $this->info('📊 Current Database State:');

        $successCount = StudentSuccess::where('student_id', $this->testStudent->id)->count();
        $approvedCount = StudentSuccess::where('student_id', $this->testStudent->id)
            ->where('success', 1)->count();
        $ratingCount = CourseRating::where('student_id', $this->testStudent->id)->count();

        $pendingEvaluations = $approvedCount - $ratingCount;

        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Submissions', $successCount],
                ['Approved/Completed', $approvedCount],
                ['Ratings Submitted', $ratingCount],
                ['Pending Evaluations', max(0, $pendingEvaluations)],
            ]
        );

        $this->newLine();
        $this->info('🎯 Expected Notification Badge Count: ' . max(0, $pendingEvaluations));

        if ($pendingEvaluations > 0) {
            $this->warn('⚠️  Red badge SHOULD be visible');
            $this->warn('⚠️  Alert banner SHOULD be displayed');
        } else {
            $this->info('✓ Red badge should NOT be visible');
            $this->info('✓ Alert banner should NOT be displayed');
        }
    }

    /**
     * Get or create test student
     */
    private function getOrCreateTestStudent($email)
    {
        $student = \App\Models\Student::where('email', $email)->first();

        if (!$student) {
            $this->info("Creating new test student: {$email}");

            $student = \App\Models\Student::create([
                'first_name' => 'Test',
                'last_name' => 'Student',
                'email' => $email,
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
        }

        return $student;
    }

    /**
     * Clean up test data
     */
    private function cleanup()
    {
        if (!$this->testStudent) {
            return;
        }

        StudentSuccess::where('student_id', $this->testStudent->id)->delete();
        CourseRating::where('student_id', $this->testStudent->id)->delete();

        $this->line('🧹 Cleaned up previous test data');
    }
}
