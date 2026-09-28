<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizProduct extends Model
{
    protected $fillable = [
        'product_id',
        'quiz_id',
        'nb_question_affiche',
        'score_success',
        'installment_month',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
