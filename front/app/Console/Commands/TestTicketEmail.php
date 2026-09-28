<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Models\Student;
use App\Mail\AdminNewTicketNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestTicketEmail extends Command
{
    protected $signature = 'test:ticket-email {email?}';
    protected $description = 'Send test support ticket email to admin';

    public function handle()
    {
        $student = Student::first();

        if (!$student) {
            $this->error('No students found in database!');
            return 1;
        }

        $testEmail = $this->argument('email') ?: config('mail.admin_email', env('ADMIN_EMAIL'));

        // Create a mock ticket for testing
        $ticket = new Ticket([
            'id' => 123,
            'student_id' => $student->id,
            'sujet' => 'Test Support Request - Payment Issue',
            'ticket_iscomplet' => false,
            'created_at' => now()
        ]);

        // Load student relationship
        $ticket->setRelation('student', $student);

        // Create mock first response
        $mockResponse = new \App\Models\ResponseTicket([
            'message' => "Hello support team,\n\nI'm having trouble accessing my course after making a payment. The transaction was successful but I still don't see the course in my dashboard.\n\nCould you please help me resolve this issue?\n\nThank you!"
        ]);

        $ticket->setRelation('responses', collect([$mockResponse]));

        $this->info('Sending test support ticket email...');

        try {
            Mail::to($testEmail)->send(new AdminNewTicketNotification($ticket));
            $this->info("✓ Test email sent to: {$testEmail}");
            $this->info('');
            $this->info('Check your email inbox (or Mailtrap) to see the template!');
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
