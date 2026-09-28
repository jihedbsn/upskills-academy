<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageProduct extends Model
{
    protected $table = 'package_products';

    protected $fillable = [
        'package_id',
        'product_id',
        'valeur_reduction',
        'discount_type',
        'fixed_discount',
        'is_active'
    ];

    protected $casts = [
        'valeur_reduction' => 'decimal:2',
        'fixed_discount' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Relation avec le package
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Relation avec le produit
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope pour les produits actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Obtenir la réduction formatée
     */
    public function getFormattedReductionAttribute(): string
    {
        return $this->valeur_reduction . '%';
    }

    /**
     * Obtenir le prix après réduction
     */
    public function getDiscountedPriceAttribute(): float
    {
        if ($this->product) {
            $originalPrice = $this->product->prix;
            $discount = ($originalPrice * $this->valeur_reduction) / 100;
            return $originalPrice - $discount;
        }
        return 0;
    }

    /**
     * Obtenir le montant de la réduction
     */
    public function getDiscountAmountAttribute(): float
    {
        if ($this->product) {
            $originalPrice = $this->product->prix;
            return ($originalPrice * $this->valeur_reduction) / 100;
        }
        return 0;
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
