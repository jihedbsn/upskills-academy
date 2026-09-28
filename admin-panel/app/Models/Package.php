<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Package extends Model
{
    protected $fillable = [
        'title',
        'title_ar',
        'image',
        'description',
        'description_ar',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Relation avec les produits du package
     */
    public function packageProducts(): HasMany
    {
        return $this->hasMany(PackageProduct::class);
    }

    /**
     * Relation avec les produits via packageProducts
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'package_products', 'package_id', 'product_id')
                    ->withPivot(['valeur_reduction', 'is_active'])
                    ->withTimestamps();
    }

    /**
     * Scope pour les packages actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Obtenir le chemin complet de l'image
     */
    public function getImagePathAttribute(): string
    {
        if ($this->image) {
            return asset('/uploads/package/' . $this->image);
        }
        return asset('/assets/images/placeholder.png');
    }

    /**
     * Obtenir le nombre de produits dans le package
     */
    public function getProductsCountAttribute(): int
    {
        return $this->packageProducts()->count();
    }

    /**
     * Obtenir le nombre de produits actifs dans le package
     */
    public function getActiveProductsCountAttribute(): int
    {
        return $this->packageProducts()->where('is_active', true)->count();
    }

    /**
     * Obtenir la réduction moyenne du package
     */
    public function getAverageReductionAttribute(): float
    {
        return $this->packageProducts()->where('is_active', true)->avg('valeur_reduction') ?? 0;
    }

    /**
     * Obtenir le statut formaté
     */
    public function getStatusAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    /**
     * Obtenir la couleur du statut
     */
    public function getStatusColorAttribute(): string
    {
        return $this->is_active ? 'success' : 'secondary';
    }
}
