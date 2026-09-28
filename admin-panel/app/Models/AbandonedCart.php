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
     * Check if first reminder should be sent (1 hour after abandonment).
     */
    public function shouldSendFirstReminder()
    {
        return !$this->converted
            && is_null($this->first_reminder_sent_at)
            && $this->abandoned_at->diffInHours(now()) >= 1;
    }

    /**
     * Check if second reminder should be sent (24 hours after abandonment).
     */
    public function shouldSendSecondReminder()
    {
        return !$this->converted
            && !is_null($this->first_reminder_sent_at)
            && is_null($this->second_reminder_sent_at)
            && $this->abandoned_at->diffInHours(now()) >= 24;
    }

    /**
     * Check if third reminder should be sent (3 days after abandonment).
     */
    public function shouldSendThirdReminder()
    {
        return !$this->converted
            && !is_null($this->second_reminder_sent_at)
            && is_null($this->third_reminder_sent_at)
            && $this->abandoned_at->diffInDays(now()) >= 3;
    }

    /**
     * Mark the cart as converted.
     */
    public function markAsConverted()
    {
        $this->update([
            'converted' => true,
            'converted_at' => now(),
        ]);
    }

    /**
     * Get the conversion rate percentage.
     */
    public static function getConversionRate()
    {
        $total = self::count();
        if ($total === 0) {
            return 0;
        }

        $converted = self::where('converted', true)->count();
        return round(($converted / $total) * 100, 2);
    }
}
