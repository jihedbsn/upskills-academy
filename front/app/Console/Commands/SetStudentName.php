<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetStudentName extends Command
{
    protected $signature = 'users:set-name {student} {first_name} {last_name}';
    protected $description = 'Manually set a student\'s first and last name';

    public function handle()
    {
        $studentFilter = $this->argument('student');
        $firstName = $this->argument('first_name');
        $lastName = $this->argument('last_name');

        try {
            // Find student by ID or email
            $query = DB::table('students');

            if (is_numeric($studentFilter)) {
                $query->where('id', $studentFilter);
            } else {
                $query->where('email', $studentFilter);
            }

            $student = $query->first();

            if (!$student) {
                $this->error("Student not found!");
                return 1;
            }

            $this->info("Found student:");
            $this->line("  ID: {$student->id}");
            $this->line("  Email: {$student->email}");
            $this->line("  Current Name: '{$student->first_name}' '{$student->last_name}'");
            $this->line("  New Name: '{$firstName}' '{$lastName}'");
            $this->newLine();

            if (!$this->confirm('Do you want to update this student\'s name?', true)) {
                $this->info('Cancelled.');
                return 0;
            }

            DB::table('students')
                ->where('id', $student->id)
                ->update([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'updated_at' => now(),
                ]);

            $this->info("✓ Student name updated successfully!");
            return 0;

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
}
