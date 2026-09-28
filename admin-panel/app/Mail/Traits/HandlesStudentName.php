<?php

namespace App\Mail\Traits;

trait HandlesStudentName
{
    /**
     * Get student name from various field formats
     */
    protected function getStudentName($student): string
    {
        if (!$student) {
            return 'Student';
        }

        // Try first_name + last_name
        if (isset($student->first_name) && isset($student->last_name)) {
            return trim($student->first_name . ' ' . $student->last_name);
        }
        
        // Try prenom + nom
        if (isset($student->prenom) && isset($student->nom)) {
            return trim($student->prenom . ' ' . $student->nom);
        }
        
        // Try just first_name
        if (isset($student->first_name)) {
            return $student->first_name;
        }
        
        // Try just prenom
        if (isset($student->prenom)) {
            return $student->prenom;
        }
        
        // Fallback
        return 'Student';
    }
}
