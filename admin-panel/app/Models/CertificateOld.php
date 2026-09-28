<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateOld extends Model
{
    use HasFactory;

    protected $table = 'certificates_old';

    // Disable auto-increment since IDs are coming from the old database
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name_ar',
        'name_en',
        'lecturer_ar',
        'lecturer_en',
        'description_ar',
        'description_en',
        'qrcodex',
        'qrcodey',
        'image',
        'image_width',
        'image_height',
        'image_real_height',
        'confirmed',
        'date',
    ];

    protected $casts = [
        'id' => 'integer',
        'qrcodex' => 'integer',
        'qrcodey' => 'integer',
        'image_width' => 'integer',
        'image_height' => 'integer',
        'image_real_height' => 'integer',
        'confirmed' => 'integer',
    ];

    /**
     * Get the full image URL.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset($this->image);
        }
        return '';
    }
}
