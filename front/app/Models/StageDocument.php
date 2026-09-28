<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StageDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'document_type',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    const TYPE_REQUEST_LETTER = 'request_letter';
    const TYPE_EVALUATION_FORM = 'evaluation_form';

    /**
     * Relationship with Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get document type label based on locale
     */
    public function getTypeNameAttribute()
    {
        $labels = [
            'request_letter' => [
                'ar' => 'خطاب الطلب الرسمي',
                'en' => 'Official Request Letter',
                'fr' => 'Lettre de demande officielle'
            ],
            'evaluation_form' => [
                'ar' => 'استمارة تقييم التدريب',
                'en' => 'Internship Evaluation Form',
                'fr' => 'Formulaire d\'évaluation de stage'
            ]
        ];

        $locale = app()->getLocale();
        return $labels[$this->document_type][$locale] ?? $this->document_type;
    }

    /**
     * Get formatted file size
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get download URL
     */
    public function getDownloadUrlAttribute()
    {
        // Generate direct URL to storage file
        return asset('storage/' . $this->file_path);
    }
}
