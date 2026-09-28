<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_programs',
        'registered_students',
        'academy_books',
        'ready_instructors',
    ];

    protected $casts = [
        'training_programs' => 'integer',
        'registered_students' => 'integer',
        'academy_books' => 'integer',
        'ready_instructors' => 'integer',
    ];

    /**
     * Get the single achievement record or create it if it doesn't exist.
     */
    public static function getInstance()
    {
        $achievement = self::first();
        
        if (!$achievement) {
            $achievement = self::create([
                'training_programs' => 0,
                'registered_students' => 0,
                'academy_books' => 0,
                'ready_instructors' => 0,
            ]);
        }
        
        return $achievement;
    }

    /**
     * Get formatted training programs count.
     */
    public function getFormattedTrainingProgramsAttribute(): string
    {
        return number_format($this->training_programs);
    }

    /**
     * Get formatted registered students count.
     */
    public function getFormattedRegisteredStudentsAttribute(): string
    {
        return number_format($this->registered_students);
    }

    /**
     * Get formatted academy books count.
     */
    public function getFormattedAcademyBooksAttribute(): string
    {
        return number_format($this->academy_books);
    }

    /**
     * Get formatted ready instructors count.
     */
    public function getFormattedReadyInstructorsAttribute(): string
    {
        return number_format($this->ready_instructors);
    }
}
