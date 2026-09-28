<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReponseQuestion extends Model
{
    protected $fillable = [
        'titre_ar',
        'titre_en',
        'question_id',
        'is_correcte'
    ];

    protected $casts = [
        'is_correcte' => 'boolean'
    ];

    /**
     * Relation avec la question
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Accesseur pour obtenir le titre de la réponse selon la langue actuelle
     */
    public function getReponseAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->titre_ar : $this->titre_en;
    }

    /**
     * Accesseur pour obtenir le titre de la réponse en arabe
     */
    public function getReponseArAttribute()
    {
        return $this->titre_ar;
    }

    /**
     * Accesseur pour obtenir le titre de la réponse en anglais
     */
    public function getReponseEnAttribute()
    {
        return $this->titre_en;
    }

    /**
     * Accesseur pour vérifier si la réponse est correcte
     */
    public function getIsCorrectAttribute()
    {
        return $this->is_correcte;
    }
}
