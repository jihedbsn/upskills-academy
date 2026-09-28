<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointsTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'order_id',
        'type',
        'points',
        'amount',
        'description',
        'balance_after',
    ];

    protected $casts = [
        'points' => 'integer',
        'amount' => 'decimal:2',
        'balance_after' => 'integer',
    ];

    const TYPE_EARN = 'earn';
    const TYPE_REDEEM = 'redeem';
    const TYPE_ADJUST = 'adjust';
    const TYPE_EXPIRE = 'expire';

    /**
     * Get the student that owns the transaction.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the order associated with the transaction.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scope for earning transactions.
     */
    public function scopeEarned($query)
    {
        return $query->where('type', self::TYPE_EARN);
    }

    /**
     * Scope for redemption transactions.
     */
    public function scopeRedeemed($query)
    {
        return $query->where('type', self::TYPE_REDEEM);
    }

    /**
     * Get the type label for display.
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            self::TYPE_EARN => 'Earned',
            self::TYPE_REDEEM => 'Redeemed',
            self::TYPE_ADJUST => 'Adjustment',
            self::TYPE_EXPIRE => 'Expired',
            default => ucfirst($this->type),
        };
    }

    /**
     * Get the type badge class for display.
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return match($this->type) {
            self::TYPE_EARN => 'bg-success',
            self::TYPE_REDEEM => 'bg-danger',
            self::TYPE_ADJUST => 'bg-warning',
            self::TYPE_EXPIRE => 'bg-secondary',
            default => 'bg-info',
        };
    }
}
