<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbandonedCart extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'total_amount',
        'items_count',
        'abandoned_at',
        'first_reminder_sent_at',
        'second_reminder_sent_at',
        'third_reminder_sent_at',
        'converted',
        'converted_at',
        'discount_coupon',
    ];

    protected $casts = [
        'abandoned_at' => 'datetime',
        'first_reminder_sent_at' => 'datetime',
        'second_reminder_sent_at' => 'datetime',
        'third_reminder_sent_at' => 'datetime',
        'converted' => 'boolean',
        'converted_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the student that owns the abandoned cart.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the items for the abandoned cart.
     */
    public function items()
    {
        return $this->hasMany(AbandonedCartItem::class);
    }

    /**
     * Check if the 3rd reminder coupon is still valid (within 24 hours)
     */
    public function isCouponStillValid(): bool
    {
        if (!$this->third_reminder_sent_at) {
            return false;
        }

        return $this->third_reminder_sent_at->diffInHours(now()) < 24;
    }
}
