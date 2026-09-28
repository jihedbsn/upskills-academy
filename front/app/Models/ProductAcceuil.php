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
        return $query->with(['product', 'product.variations', 'product.teacher']);
    }

    // Accesseur pour l'URL de l'image du produit
    public function getImageUrlAttribute()
    {
        if ($this->product && $this->product->images) {
            $baseUrl = (config('app.file_url') ?: asset('uploads/products/images/'));
            return $baseUrl . 'uploads/products/images/' . $this->product->images;
        }
        return null;
    }

    // Accesseur pour le titre selon la langue
    public function getTitleAttribute()
    {
        $variation = $this->product->variations->where('langue', app()->getLocale())->first();
        if ($variation) {
            return $variation->name;
        }
        return null;
    }

    // Accesseur pour la description selon la langue
    public function getDescriptionAttribute()
    {
        $variation = $this->product->variations->where('langue', app()->getLocale())->first();
        if ($variation) {
            return $variation->short_description;
        }
        return null;
    }

    // Accesseur pour le nom du teacher
    public function getTeacherNameAttribute()
    {
        if ($this->product && $this->product->teacher) {
            return $this->product->teacher->nom . ' ' . $this->product->teacher->prenom;
        }
        return null;
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }
}
