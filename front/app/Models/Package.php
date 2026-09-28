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
     * Obtenir le prix total du package (somme des prix des produits)
     */
    public function getTotalPriceAttribute(): float
    {
        return $this->packageProducts()
            ->where('is_active', true)
            ->with('product')
            ->get()
            ->sum(function($packageProduct) {
                return $packageProduct->product->prix ?? 0;
            });
    }

    /**
     * Obtenir le prix minimum des produits du package
     */
    public function getMinPriceAttribute(): float
    {
        return $this->packageProducts()
            ->where('is_active', true)
            ->with('product')
            ->get()
            ->min(function($packageProduct) {
                return $packageProduct->product->prix ?? 0;
            }) ?? 0;
    }

    /**
     * Obtenir le prix formaté du package
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->total_price, 2);
    }

    /**
     * Obtenir la réduction moyenne du package (en pourcentage)
     */
    public function getAverageReductionAttribute(): float
    {
        $products = $this->packageProducts()
            ->where('is_active', true)
            ->with('product')
            ->get();

        $totalOriginal = 0;
        $totalDiscount = 0;

        foreach ($products as $packageProduct) {
            $price = $packageProduct->product->prix ?? 0;
            $discountType = $packageProduct->discount_type ?? 'percentage';

            $totalOriginal += $price;

            if ($discountType === 'fixed') {
                // Fixed discount amount
                $discount = $packageProduct->fixed_discount ?? 0;
                $totalDiscount += $discount;
            } else {
                // Percentage discount
                $reduction = $packageProduct->valeur_reduction ?? 0;
                $totalDiscount += ($price * $reduction) / 100;
            }
        }

        if ($totalOriginal <= 0) {
            return 0;
        }

        return ($totalDiscount / $totalOriginal) * 100;
    }

    /**
     * Obtenir le prix total après réduction
     */
    public function getDiscountedPriceAttribute(): float
    {
        return $this->packageProducts()
            ->where('is_active', true)
            ->with('product')
            ->get()
            ->sum(function ($packageProduct) {
                $price = $packageProduct->product->prix ?? 0;
                $discountType = $packageProduct->discount_type ?? 'percentage';

                if ($discountType === 'fixed') {
                    $discount = $packageProduct->fixed_discount ?? 0;
                } else {
                    $reduction = $packageProduct->valeur_reduction ?? 0;
                    $discount = ($price * $reduction) / 100;
                }

                return max(0, $price - $discount);
            });
    }

    /**
     * Obtenir le montant total économisé
     */
    public function getSavingsAmountAttribute(): float
    {
        return $this->total_price - $this->discounted_price;
    }

    /**
     * Obtenir le statut formaté
     */
    public function getStatusAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    /**
     * Get the formatted title (clean up duplicate percentage symbols)
     */
    public function getFormattedTitleAttribute(): string
    {
        $locale = app()->getLocale();
        $title = ($locale === 'ar' && $this->attributes['title_ar'] ?? null)
            ? $this->attributes['title_ar']
            : ($this->attributes['title'] ?? '');

        // Remove duplicate percentage symbols (e.g., "%15%" becomes "15%")
        return preg_replace('/%(\d+)%/', '$1%', $title);
    }

    /**
     * Obtenir la couleur du statut
     */
    public function getStatusColorAttribute(): string
    {
        return $this->is_active ? 'success' : 'secondary';
    }
}
