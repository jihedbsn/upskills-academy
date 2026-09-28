<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quiz extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'score',
        'type_id',
        'max_attempts'
    ];

    /**
     * Relation avec le type de quiz
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(TypeQuiz::class, 'type_id');
    }

    /**
     * Relation many-to-many avec les produits
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'quiz_products', 'quiz_id', 'product_id');
    }

    /**
     * Relation one-to-many avec les questions
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
