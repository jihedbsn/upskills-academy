<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'nom_en',
        'prenom_en',
        'email',
        // Keep both for backward compatibility; controller will use 'image'
        'images',
        'image',
        'password',
    ];

    /**
     * Get the products for the teacher.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'teacher_id');
    }

    /**
     * Accessor to expose 'image' while storing in legacy 'images' column.
     */
    public function getImageAttribute()
    {
        return $this->attributes['images'] ?? null;
    }

    /**
     * Mutator to set 'image' while persisting to 'images' column.
     */
    public function setImageAttribute($value): void
    {
        $this->attributes['images'] = $value;
    }

    /**
     * Computed full URL for the image with fallback.
     */
    public function getImageUrlAttribute(): string
    {
        $path = $this->image;
        if (!empty($path)) {
            if (Str::startsWith($path, ['http://', 'https://', '/'])) {
                return $path;
            }
            if (Str::startsWith($path, 'uploads/')) {
                return asset($path);
            }
            if (Str::startsWith($path, 'teachers/')) {
                // Legacy storage path (storage/app/public/teachers/*)
                return asset('storage/' . $path);
            }
            return asset($path);
        }
        return asset('no-image/1.jpg');
    }
}
