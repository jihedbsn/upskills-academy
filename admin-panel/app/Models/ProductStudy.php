<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStudy extends Model
{
    use HasFactory;

    protected $fillable = [
        'products_id',
        'name_ar',
        'name_en',
        'resource_id',
        'lang',
        'order',
        'opens_after_purchase_days',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'products_id');
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class, 'resource_id');
    }

    // Accesseur pour le nom multilingue
    public function getNameAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }

    // Accesseur pour la durée (depuis la ressource)
    public function getDurationAttribute()
    {
        if ($this->resource && $this->resource->duration) {
            return $this->resource->duration;
        }
        return null;
    }
}
