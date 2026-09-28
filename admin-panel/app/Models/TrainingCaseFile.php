<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingCaseFile extends Model
{
    protected $fillable = [
        'training_case_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'order',
    ];

    /**
     * Get the training case that owns this file
     */
    public function trainingCase()
    {
        return $this->belongsTo(TrainingCase::class);
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSizeHumanAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
