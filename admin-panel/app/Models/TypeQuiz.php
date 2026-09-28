<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeQuiz extends Model
{
    protected $fillable = [
        'titre'
    ];

    /**
     * Relation avec les quizzes
     */
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class, 'type_id');
    }
}
