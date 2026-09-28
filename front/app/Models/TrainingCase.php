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
    ];

    public function files()
    {
        return $this->hasMany(TrainingCaseFile::class);
    }

    public function practicalExamAttempts()
    {
        return $this->hasMany(PracticalExamAttempt::class);
    }
}
