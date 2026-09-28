<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;

class TestWelcomeEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test-welcome {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test welcome email to verify the design';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Welcome Email...');

        // Get email address from argument or ask
        $email = $this->argument('email');

        if (!$email) {
            $email = $this->ask('Enter the email address to send the test to');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address!');
            return 1;
        }

        // Create a test student object
        $testStudent = (object) [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => $email,
            'country' => 'Sweden',
        ];

        try {
            // Send the email
            Mail::to($email)->send(new WelcomeEmail($testStudent));

            $this->info('✓ Test welcome email sent successfully to: ' . $email);
            $this->line('');
            $this->line('Please check your inbox to verify the email design.');
            $this->line('The email should display:');
            $this->line('  - Professional blue header with Upskills Academy branding');
            $this->line('  - Clean, readable content without emojis');
            $this->line('  - Account information in a blue info card');
            $this->line('  - Available features in a green success card');
            $this->line('  - Access Dashboard button');
            $this->line('  - Professional footer with links and copyright');

            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
            $this->line('');
            $this->line('Make sure your mail configuration is set up correctly in .env file:');
            $this->line('  MAIL_MAILER=smtp');
            $this->line('  MAIL_HOST=your-smtp-host');
            $this->line('  MAIL_PORT=587');
            $this->line('  MAIL_USERNAME=your-email');
            $this->line('  MAIL_PASSWORD=your-password');

            return 1;
        }
    }
}
