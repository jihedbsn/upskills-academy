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
            'fa' => 'Foundation English (FA)',
            'fi' => 'Fluency & Interaction (FI)',
            'pt' => 'Proficiency Track (PT)',
            'autres' => 'Autres'
        ];

        // Return the type name if it exists, otherwise return a visible default
        return !empty($this->type) && isset($types[$this->type])
            ? $types[$this->type]
            : 'Non défini';
    }

    public function getTypeColorAttribute()
    {
        $colors = [
            'fa' => 'success',
            'fi' => 'info',
            'pt' => 'warning',
            'autres' => 'secondary'
        ];

        // Return the color if type exists, otherwise return a visible default
        return !empty($this->type) && isset($colors[$this->type])
            ? $colors[$this->type]
            : 'secondary';
    }
}
