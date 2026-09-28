<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory;

    protected $fillable = [
        'products_id',
        'name',
        'slug',
        'short_description',
        'ad',
        'description_the_exams',
        'description_the_quizzes',
        'description_final_exam',
        'description_video_exam',
        'description_stage',
        'description_study_case',
        'langue',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'products_id');
    }

    /**
     * Scope to get variations by language
     */
    public function scopeByLanguage($query, $language = null)
    {
        $lang = $language ?: app()->getLocale();
        return $query->where('langue', $lang);
    }
}
