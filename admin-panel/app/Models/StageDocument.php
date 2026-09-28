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

    public static $documentTypes = [
        self::TYPE_REQUEST_LETTER => 'خطاب الطلب',
        self::TYPE_EVALUATION_FORM => 'استمارة التقييم',
    ];

    /**
     * Relationship with Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get document type label
     */
    public function getTypeNameAttribute()
    {
        return self::$documentTypes[$this->document_type] ?? $this->document_type;
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
        // Generate direct URL to storage file, removing /admin if present
        $baseUrl = url('/');
        // Remove /admin from the base URL if it exists
        $baseUrl = preg_replace('/\/admin$/', '', $baseUrl);
        return $baseUrl . '/storage/' . $this->file_path;
    }
}
