<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'titre_ar',
        'titre_en',
        'description_short_ar',
        'description_short_en',
        'description_ar',
        'description_en',
        'file',
        'summary',
        'image',
        'prix'
    ];

    protected $casts = [
        'prix' => 'decimal:2'
    ];

    /**
     * Accesseur pour obtenir le titre selon la langue
     */
    public function getTitreAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->titre_ar : $this->titre_en;
    }

    /**
     * Accesseur pour obtenir la description courte selon la langue
     */
    public function getDescriptionShortAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->description_short_ar : $this->description_short_en;
    }

    /**
     * Accesseur pour obtenir la description complète selon la langue
     */
    public function getDescriptionAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->description_ar : $this->description_en;
    }

    /**
     * Accesseur pour obtenir l'URL du fichier
     */
    public function getFileUrlAttribute()
    {
        if ($this->file) {
            return asset('uploads/books/' . $this->file);
        }
        return null;
    }

    /**
     * Accesseur pour obtenir l'URL du fichier summary
     */
    public function getSummaryUrlAttribute()
    {
        if ($this->summary) {
            return asset('uploads/books/summaries/' . $this->summary);
        }
        return null;
    }

    /**
     * Accesseur pour obtenir l'URL de l'image
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('uploads/books/images/' . $this->image);
        }
        return asset('uploads/books/images/no-image.jpg'); // Default image
    }

    /**
     * Accesseur pour obtenir le prix formaté
     */
    public function getPrixFormattedAttribute()
    {
        return '$' . number_format($this->prix, 2);
    }
}
