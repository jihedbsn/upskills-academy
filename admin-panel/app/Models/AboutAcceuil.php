<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutAcceuil extends Model
{
    use HasFactory;

    protected $table = 'about_acceuil';

    protected $fillable = [
        'description_en',
        'description_ar',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($about) {
            // S'assurer que is_active a une valeur par défaut
            if (!isset($about->is_active)) {
                $about->is_active = true;
            }
        });
    }

    // Accesseur pour le contenu multilingue
    public function getDescriptionAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->description_ar : $this->description_en;
    }

    // Scopes pour les requêtes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('description_ar', 'like', "%{$search}%")
              ->orWhere('description_en', 'like', "%{$search}%");
        });
    }
}
