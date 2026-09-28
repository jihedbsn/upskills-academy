<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'status',
        'subject',
        'content',
        'text_content',
        'variables',
        'description',
        'is_active'
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean'
    ];

    // Types de templates disponibles
    const TYPES = [
        'quiz' => 'Quiz/Exam Notifications',
        'stage' => 'Internship Notifications', 
        'video_exam' => 'Video Exam Notifications',
        'student_success' => 'Final Success Notifications',
        'student' => 'Student Account & Enrollment',
        'custom' => 'Custom Email'
    ];

    // Statuts disponibles
    const STATUSES = [
        'validated' => 'Validated/Approved',
        'rejected' => 'Rejected/Revision Required',
        'approved' => 'Final Approval',
        'requirements' => 'Additional Requirements',
        'account_created' => 'Account Created',
        'course_enrolled' => 'Course Enrollment',
        'custom' => 'Custom Status'
    ];

    /**
     * Scope pour les templates actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope par type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope par statut
     */
    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Obtenir un template spécifique
     */
    public static function getTemplate($type, $status)
    {
        return static::active()
            ->where('type', $type)
            ->where('status', $status)
            ->first();
    }

    /**
     * Remplacer les variables dans le contenu
     */
    public function renderContent($variables = [])
    {
        $content = $this->content;
        
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }
        
        return $content;
    }

    /**
     * Remplacer les variables dans le sujet
     */
    public function renderSubject($variables = [])
    {
        $subject = $this->subject;
        
        foreach ($variables as $key => $value) {
            $subject = str_replace('{{' . $key . '}}', $value, $subject);
        }
        
        return $subject;
    }

    /**
     * Obtenir la liste des variables disponibles
     */
    public function getAvailableVariables()
    {
        return $this->variables ?? [];
    }

    /**
     * Obtenir le nom du type
     */
    public function getTypeNameAttribute()
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * Obtenir le nom du statut
     */
    public function getStatusNameAttribute()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}