<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAcceuil extends Model
{
    use HasFactory;

    protected $table = 'products_acceuil';

    protected $fillable = [
        'product_id',
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
        
        static::creating(function ($productAcceuil) {
            // S'assurer que is_active a une valeur par défaut
            if (!isset($productAcceuil->is_active)) {
                $productAcceuil->is_active = true;
            }
            // S'assurer que order a une valeur par défaut
            if (!isset($productAcceuil->order)) {
                $productAcceuil->order = 0;
            }
        });
    }

    // Relation avec le modèle Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relation avec ProductVariation via Product (pour l'affichage)
    public function productVariation()
    {
        return $this->hasOneThrough(ProductVariation::class, Product::class, 'id', 'products_id', 'product_id', 'id');
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

    public function scopeWithProduct($query)
    {
        return $query->with(['product', 'product.variations']);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }
}
