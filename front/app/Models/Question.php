<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'point',
        'quiz_id'
    ];

    protected $attributes = [
        'point' => 10
    ];

    /**
     * Relation avec le quiz
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Relation one-to-many avec les réponses
     */
    public function reponses(): HasMany
    {
        return $this->hasMany(ReponseQuestion::class);
    }

    /**
     * Accesseur pour obtenir le nom de la question selon la langue actuelle
     */
    public function getQuestionAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }

    /**
     * Accesseur pour obtenir le nom de la question en arabe
     */
    public function getQuestionArAttribute()
    {
        return $this->name_ar;
    }

    /**
     * Accesseur pour obtenir le nom de la question en anglais
     */
    public function getQuestionEnAttribute()
    {
        return $this->name_en;
    }
}
