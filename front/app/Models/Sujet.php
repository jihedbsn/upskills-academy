<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sujet extends Model
{
    protected $fillable = [
        'description',
        'lang',
        'type'
    ];

    protected $casts = [
        'description' => 'string',
        'lang' => 'string',
        'type' => 'string'
    ];

    // Scopes
    public function scopeArabic($query)
    {
        return $query->where('lang', 'ar');
    }

    public function scopeEnglish($query)
    {
        return $query->where('lang', 'en');
    }

    public function scopeByLanguage($query, $lang)
    {
        return $query->where('lang', $lang);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeFitnessAssistant($query)
    {
        return $query->where('type', 'fa');
    }

    public function scopeFitnessInstructor($query)
    {
        return $query->where('type', 'fi');
    }

    public function scopePersonalTrainer($query)
    {
        return $query->where('type', 'pt');
    }

    public function scopeAutres($query)
    {
        return $query->where('type', 'autres');
    }

    // Accesseurs
    public function getLanguageNameAttribute()
    {
        return $this->lang === 'ar' ? 'العربية' : 'English';
    }

    public function getShortDescriptionAttribute()
    {
        return strlen($this->description) > 100 
            ? substr($this->description, 0, 100) . '...' 
            : $this->description;
    }

    public function getTypeNameAttribute()
    {
        $types = [
            'fa' => 'Fitness Assistant (FA)',
            'fi' => 'Fitness Instructor (FI)', 
            'pt' => 'Personal Trainer (PT)',
            'autres' => 'Autres'
        ];
        
        return $types[$this->type] ?? 'Non défini';
    }

    public function getTypeColorAttribute()
    {
        $colors = [
            'fa' => 'success',
            'fi' => 'info',
            'pt' => 'warning',
            'autres' => 'secondary'
        ];
        
        return $colors[$this->type] ?? 'light';
    }
}
