<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all files for this training case
     */
    public function files()
    {
        return $this->hasMany(TrainingCaseFile::class)->orderBy('order');
    }

    /**
     * Get all products using this training case
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_training_cases');
    }

    /**
     * Get all attempts using this training case
     */
    public function attempts()
    {
        return $this->hasMany(PracticalExamAttempt::class);
    }

    /**
     * Scope to get only active training cases
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
