<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingCaseFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_case_id',
        'original_name',
        'file_path',
    ];

    public function trainingCase()
    {
        return $this->belongsTo(TrainingCase::class);
    }
}
