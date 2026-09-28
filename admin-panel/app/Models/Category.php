<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::addGlobalScope('ordered', function ($builder) {
            $builder->orderBy('order', 'asc');
        });
    }

    protected $fillable = [
        'titre',
        'titre_ar',
        'titre_en',
        'order',
    ];

    /**
     * Get the products for the category.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'categories_id');
    }

    /**
     * Get the coupons associated with the category (many-to-many).
     */
    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_category', 'category_id', 'coupon_id')
                    ->withTimestamps();
    }

    /**
     * Get the localized title based on current locale.
     */
    public function getLocalizedTitleAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->titre_ar : $this->titre_en;
    }
}
