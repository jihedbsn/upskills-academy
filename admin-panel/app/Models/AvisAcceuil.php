<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvisAcceuil extends Model
{
    use HasFactory;

    protected $table = 'avis_acceuil';

    protected $fillable = [
        'client',
        'image',
        'avis_en',
        'avis_ar',
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
        
        static::creating(function ($avis) {
            // S'assurer que is_active a une valeur par défaut
            if (!isset($avis->is_active)) {
                $avis->is_active = true;
            }
            // S'assurer que order a une valeur par défaut
            if (!isset($avis->order)) {
                $avis->order = 0;
            }
        });
    }

    // Accesseur pour le contenu multilingue
    public function getAvisAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->avis_ar : $this->avis_en;
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
            $q->where('client', 'like', "%{$search}%")
              ->orWhere('avis_ar', 'like', "%{$search}%")
              ->orWhere('avis_en', 'like', "%{$search}%");
        });
    }
}
