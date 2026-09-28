<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre_ar',
        'titre_en',
        'meta_title_ar',
        'meta_title_en',
        'description_ar',
        'description_en',
        'description_short_ar',
        'description_short_en',
        'slug',
        'image',
        'author_ar',
        'author_en',
        'published_date',
        'is_active',
        'views_count'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'published_date' => 'date',
        'views_count' => 'integer'
    ];

    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $blog->slug = Str::slug($blog->titre_en);
            }
            // S'assurer que is_active a une valeur par défaut
            if (!isset($blog->is_active)) {
                $blog->is_active = true;
            }
        });
        
        static::updating(function ($blog) {
            if ($blog->isDirty('titre_en') && empty($blog->slug)) {
                $blog->slug = Str::slug($blog->titre_en);
            }
        });
    }

    // Accesseurs pour le contenu multilingue
    public function getTitreAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->titre_ar : $this->titre_en;
    }

    public function getMetaTitleAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->meta_title_ar : $this->meta_title_en;
    }

    public function getDescriptionAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->description_ar : $this->description_en;
    }

    public function getDescriptionShortAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->description_short_ar : $this->description_short_en;
    }

    public function getAuthorAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->author_ar : $this->author_en;
    }

    // Scopes pour les requêtes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('titre_ar', 'like', "%{$search}%")
              ->orWhere('titre_en', 'like', "%{$search}%")
              ->orWhere('description_ar', 'like', "%{$search}%")
              ->orWhere('description_en', 'like', "%{$search}%");
        });
    }

    public function scopePublished($query)
    {
        return $query->where('is_active', true)
                    ->whereNotNull('published_date')
                    ->where('published_date', '<=', now());
    }

    // Méthode pour incrémenter le compteur de vues
    public function incrementViews()
    {
        $this->increment('views_count');
    }
}
