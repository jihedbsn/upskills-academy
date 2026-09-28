<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseExtensionOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'product_id',
        'price',
        'payment_method',
        'payment_status',
        'payment_success',
        'payment_receipt',
        'payment_description',
        'stripe_session_id',
        'stripe_payment_intent',
        'transaction_id',
        'extension_months',
        'old_expiration_date',
        'new_expiration_date',
        'is_processed',
        'notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'payment_success' => 'boolean',
        'is_processed' => 'boolean',
        'old_expiration_date' => 'datetime',
        'new_expiration_date' => 'datetime',
    ];

    // Relations
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('payment_status', 'approved');
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Apply the extension to the student's course access
     */
    public function applyExtension()
    {
        if ($this->is_processed) {
            return false;
        }

        $productStudent = ProductStudent::where('student_id', $this->student_id)
            ->where('product_id', $this->product_id)
            ->first();

        if (!$productStudent) {
            return false;
        }

        // Calculate new expiration date
        $baseDate = $productStudent->expiration_date && $productStudent->expiration_date->isFuture()
            ? $productStudent->expiration_date
            : now();

        $newExpirationDate = $baseDate->copy()->addMonths($this->extension_months);

        // Save old expiration date
        $this->old_expiration_date = $productStudent->expiration_date;
        $this->new_expiration_date = $newExpirationDate;

        // Update student's course access
        $productStudent->update([
            'expiration_date' => $newExpirationDate,
            'is_expired' => false,
            'is_active' => true,
            'extension_count' => ($productStudent->extension_count ?? 0) + 1,
        ]);

        // Mark as processed
        $this->is_processed = true;
        $this->save();

        return true;
    }

    /**
     * Get the status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return match ($this->payment_status) {
            'approved' => 'success',
            'pending' => 'warning',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }
}
