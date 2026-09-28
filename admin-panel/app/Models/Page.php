<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre_ar',
        'titre_en',
        'meta_title_ar',
        'meta_title_en',
        'description_ar',
        'description_en',
        'slug',
        'is_active',
        'order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    // Méthode pour générer automatiquement le slug
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->titre_en);
            }
            // S'assurer que is_active a une valeur par défaut
            if (!isset($page->is_active)) {
                $page->is_active = true;
            }
        });
        
        static::updating(function ($page) {
            if ($page->isDirty('titre_en') && empty($page->slug)) {
                $page->slug = Str::slug($page->titre_en);
            }
        });
    }

    // Accesseurs pour obtenir le titre selon la langue
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

    // Scope pour les pages actives
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope pour trier par ordre
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at', 'desc');
    }

    // Méthode pour rechercher par titre
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('titre_ar', 'like', '%' . $search . '%')
              ->orWhere('titre_en', 'like', '%' . $search . '%')
              ->orWhere('meta_title_ar', 'like', '%' . $search . '%')
              ->orWhere('meta_title_en', 'like', '%' . $search . '%');
        });
    }
}
