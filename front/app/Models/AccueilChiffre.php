<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccueilChiffre extends Model
{
    use HasFactory;

    protected $fillable = [
        'coach_ready',
        'icone_coach_ready',
        'book_of_the_academy',
        'icone_book_of_the_academy',
        'registered_student',
        'icone_registered_student',
        'training_program',
        'icone_training_program',
        'is_active',
    ];

    protected $casts = [
        'coach_ready' => 'integer',
        'book_of_the_academy' => 'integer',
        'registered_student' => 'integer',
        'training_program' => 'integer',
        'is_active' => 'boolean',
    ];

    // Accessors for image URLs
    public function getIconeCoachReadyUrlAttribute()
    {
        return $this->icone_coach_ready ? asset('uploads/accueil-chiffres/' . $this->icone_coach_ready) : null;
    }

    public function getIconeBookOfTheAcademyUrlAttribute()
    {
        return $this->icone_book_of_the_academy ? asset('uploads/accueil-chiffres/' . $this->icone_book_of_the_academy) : null;
    }

    public function getIconeRegisteredStudentUrlAttribute()
    {
        return $this->icone_registered_student ? asset('uploads/accueil-chiffres/' . $this->icone_registered_student) : null;
    }

    public function getIconeTrainingProgramUrlAttribute()
    {
        return $this->icone_training_program ? asset('uploads/accueil-chiffres/' . $this->icone_training_program) : null;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
