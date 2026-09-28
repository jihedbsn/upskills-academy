<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentStageCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'product_id',
        'file1',
        'file2',
        'description',
        'is_valid',
        'admin_notes',
        'approval_message',
        'submitted_at',
        'validated_at',
    ];

    protected $casts = [
        'is_valid' => 'integer',
        'submitted_at' => 'datetime',
        'validated_at' => 'datetime',
    ];

    // Relations
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Accessors
    public function getFile1UrlAttribute()
    {
        if ($this->file1) {
            return asset('uploads/student_stage_courses/' . $this->file1);
        }
        return null;
    }

    public function getFile2UrlAttribute()
    {
        if ($this->file2) {
            return asset('uploads/student_stage_courses/' . $this->file2);
        }
        return null;
    }

    public function getFile1ExtensionAttribute()
    {
        if ($this->file1) {
            return strtolower(pathinfo($this->file1, PATHINFO_EXTENSION));
        }
        return null;
    }

    public function getFile2ExtensionAttribute()
    {
        if ($this->file2) {
            return strtolower(pathinfo($this->file2, PATHINFO_EXTENSION));
        }
        return null;
    }

    public function getFile1TypeAttribute()
    {
        $extension = $this->file1_extension;
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            return 'image';
        } elseif ($extension === 'pdf') {
            return 'pdf';
        }
        return 'file';
    }

    public function getFile2TypeAttribute()
    {
        $extension = $this->file2_extension;
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            return 'image';
        } elseif ($extension === 'pdf') {
            return 'pdf';
        }
        return 'file';
    }

    // Statut constants
    const STATUS_PENDING = 0;
    const STATUS_VALIDATED = 1;
    const STATUS_REJECTED = -1;

    // Helper methods for status
    public function isPending()
    {
        return $this->is_valid === self::STATUS_PENDING;
    }

    public function isValidated()
    {
        return $this->is_valid === self::STATUS_VALIDATED;
    }

    public function isRejected()
    {
        return $this->is_valid === self::STATUS_REJECTED;
    }

    public function getStatusTextAttribute()
    {
        switch ($this->is_valid) {
            case self::STATUS_VALIDATED:
                return 'Validated';
            case self::STATUS_REJECTED:
                return 'Rejected';
            default:
                return 'Pending';
        }
    }

    public function getStatusClassAttribute()
    {
        switch ($this->is_valid) {
            case self::STATUS_VALIDATED:
                return 'success';
            case self::STATUS_REJECTED:
                return 'danger';
            default:
                return 'warning';
        }
    }

    // Scopes
    public function scopeValid($query)
    {
        return $query->where('is_valid', self::STATUS_VALIDATED);
    }

    public function scopePending($query)
    {
        return $query->where('is_valid', self::STATUS_PENDING);
    }

    public function scopeRejected($query)
    {
        return $query->where('is_valid', self::STATUS_REJECTED);
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }
}
