<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NosPartenaires extends Model
{
    protected $table = 'nos_partenaires';
    
    protected $fillable = [
        'nom',
        'url',
        'logo',
        'order',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer'
    ];
    
    /**
     * Scope pour récupérer les partenaires actifs triés par ordre
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
    
    /**
     * Accessor pour l'URL complète du logo
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('uploads/' . $this->logo);
        }
        return null;
    }
}
