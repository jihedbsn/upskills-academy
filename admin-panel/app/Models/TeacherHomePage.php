<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TeacherHomePage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'image',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Scope to filter active teachers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by custom order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Get the full image URL.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset('uploads/teachers/' . $this->image);
        }
        return asset('uploads/teachers/default-teacher.png');
    }

    /**
     * Delete the teacher image file.
     */
    public function deleteImage(): void
    {
        if ($this->image && file_exists(public_path('uploads/teachers/' . $this->image))) {
            unlink(public_path('uploads/teachers/' . $this->image));
        }
    }

    /**
     * Boot method to automatically delete image when model is deleted.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($teacher) {
            $teacher->deleteImage();
        });
    }
}
