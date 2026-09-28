<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Carousel extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug_ar',
        'slug_en',
        'description_ar',
        'description_en',
        'image',
        'is_active',
        'order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($carousel) {
            if (empty($carousel->slug_ar)) {
                $carousel->slug_ar = Str::slug($carousel->slug_ar);
            }
            if (empty($carousel->slug_en)) {
                $carousel->slug_en = Str::slug($carousel->slug_en);
            }
            // S'assurer que is_active a une valeur par défaut
            if (!isset($carousel->is_active)) {
                $carousel->is_active = true;
            }
            // S'assurer que order a une valeur par défaut
            if (!isset($carousel->order)) {
                $carousel->order = 0;
            }
        });
    }

    // Accesseurs pour le contenu multilingue
    public function getSlugAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->slug_ar : $this->slug_en;
    }

    public function getDescriptionAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->description_ar : $this->description_en;
    }

    // Scopes pour les requêtes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('slug_ar', 'like', "%{$search}%")
              ->orWhere('slug_en', 'like', "%{$search}%")
              ->orWhere('description_ar', 'like', "%{$search}%")
              ->orWhere('description_en', 'like', "%{$search}%");
        });
    }
}
