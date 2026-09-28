<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'nom_en',
        'prenom_en',
        'email',
        'images',
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
     * Get full image URL for the teacher profile picture.
     */
    public function getImageUrlAttribute()
    {
        if (!$this->images) {
            return asset('assets/img/avatars/small-1.png');
        }

        $base = config('app.file_url');
        if ($base) {
            return rtrim($base, '/').'/'.$this->images;
        }

        return asset('assets/img/avatars/small-1.png');
    }

    /**
     * Get full name based on current locale (ar or en).
     */
    public function getFullNameAttribute()
    {
        $locale = app()->getLocale();
        
        if ($locale === 'ar') {
            $nom = $this->nom ?? '';
            $prenom = $this->prenom ?? '';
        } else {
            $nom = $this->nom_en ?? $this->nom ?? '';
            $prenom = $this->prenom_en ?? $this->prenom ?? '';
        }
        
        return trim($nom . ' ' . $prenom);
    }
}
